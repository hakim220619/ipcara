<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\Test as TestModel;
use App\Models\TestAttempt;
use App\Models\Topic;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PreTestTest extends TestCase
{
    use RefreshDatabase;

    private User $mahasiswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
        $this->mahasiswa = User::query()->where('email', 'mahasiswa@ipcara.test')->firstOrFail();
    }

    #[Test]
    public function pre_test_berisi_25_soal_dari_lima_topik(): void
    {
        $preTest = TestModel::query()->where('kind', TestModel::KIND_PRE_TEST)->firstOrFail();

        $this->assertSame(25, $preTest->questions()->count());
        $this->assertSame(
            ['Pengertian PPI', 'Hand Hygiene', 'Penggunaan APD', 'Etika Batuk', 'Pengelolaan Limbah'],
            Topic::query()->orderBy('order')->pluck('title')->all(),
        );
        $this->assertSame(0, Question::query()->whereDoesntHave('options', fn ($q) => $q->where('is_correct', true))->count());
    }

    #[Test]
    public function mahasiswa_yang_belum_pre_test_diarahkan_ke_pre_test(): void
    {
        $this->actingAs($this->mahasiswa);

        $this->get(route('dashboard'))->assertRedirect(route('pre-test.index'));
        $this->get(route('materi.index'))->assertRedirect(route('pre-test.index'));

        $this->get(route('pre-test.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('pre-test/Index')
                ->where('test.question_count', 25)
                ->has('test.topics', 5)
                ->where('inProgress', false)
                ->where('preTestDone', false));
    }

    #[Test]
    public function soal_ditampilkan_tanpa_kunci_jawaban(): void
    {
        $this->actingAs($this->mahasiswa)
            ->post(route('pre-test.start'))
            ->assertRedirect(route('pre-test.take'));

        $this->get(route('pre-test.take'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('pre-test/Take')
                ->has('questions', 25)
                ->has('questions.0.options', 4)
                ->missing('questions.0.options.0.is_correct')
                ->whereNot('deadline', null)
                ->etc());

        $order = fn () => collect($this->get(route('pre-test.take'))->inertiaProps('questions'))->pluck('id')->all();
        $this->assertSame($order(), $order());
    }

    #[Test]
    public function mengirim_jawaban_menghitung_skor_persentase_dan_rekomendasi(): void
    {
        $this->actingAs($this->mahasiswa)->post(route('pre-test.start'));

        $topics = Topic::query()->orderBy('order')->get();
        $strongTopicIds = $topics->take(2)->pluck('id');

        $answers = Question::query()->with('options')->get()->mapWithKeys(fn (Question $q) => [
            $q->id => $q->options->firstWhere('is_correct', $strongTopicIds->contains($q->topic_id))->id,
        ]);

        $this->post(route('pre-test.submit'), ['answers' => $answers->all()])
            ->assertRedirect(route('pre-test.result'));

        $attempt = TestAttempt::query()->where('user_id', $this->mahasiswa->id)->firstOrFail();
        $this->assertSame('completed', $attempt->status);
        $this->assertSame(10, $attempt->correct_count);
        $this->assertEquals(40.0, (float) $attempt->score);
        $this->assertFalse($attempt->is_passed);

        $this->assertSame(5, $this->mahasiswa->abilityAssessments()->count());
        $recommendations = $this->mahasiswa->recommendations()->with('material')->get();
        $this->assertSame(3, $recommendations->count());
        $this->assertEqualsCanonicalizing($topics->skip(2)->pluck('id')->all(), $recommendations->pluck('topic_id')->all());
        $this->assertTrue($recommendations->every(fn ($r) => $r->material->level === 'dasar'));

        $this->get(route('pre-test.result'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('pre-test/Result')
                ->where('result.correct', 10)
                ->where('result.total', 25)
                ->where('result.percent', 40)
                ->where('result.total_points', 200)
                ->where('result.max_points', 500)
                ->where('topics.0.score', 100)
                ->where('topics.1.score', 100)
                ->where('topics.2.score', 0)
                ->has('topics', 5)
                ->where('topics.0.title', 'Pengertian PPI')
                ->where('topics.0.percent', 100)
                ->where('topics.0.recommended', false)
                ->where('topics.2.percent', 0)
                ->where('topics.2.recommended', true));

        $this->get(route('materi.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('materi/Index')
                ->has('topics', 5)
                ->where('topics.0.recommended', true)
                ->where('topics.3.recommended', false)
                ->where('topics.4.recommended', false));

        $this->get(route('materi.show', $topics->first()->materials()->first()))->assertOk();
        $this->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('preTestDone', true)
                ->where('preTestSummary.correct', 10)
                ->where('preTestSummary.total', 25)
                ->has('preTestSummary.recommended_topics', 3)
                ->etc());
    }

    #[Test]
    public function pre_test_hanya_dapat_dikerjakan_sekali(): void
    {
        $this->actingAs($this->mahasiswa)->post(route('pre-test.start'));
        $this->post(route('pre-test.submit'), ['answers' => []]);

        $this->get(route('pre-test.index'))->assertRedirect(route('pre-test.result'));
        $this->post(route('pre-test.start'))->assertRedirect(route('pre-test.result'));
        $this->post(route('pre-test.submit'), ['answers' => []])->assertRedirect(route('pre-test.index'));

        $this->assertSame(1, TestAttempt::query()->where('user_id', $this->mahasiswa->id)->count());
    }

    #[Test]
    public function adaptive_learning_mengelompokkan_topik_berdasarkan_nilai_pre_test(): void
    {
        $this->actingAs($this->mahasiswa)->post(route('pre-test.start'));

        $preTest = TestModel::query()->where('kind', TestModel::KIND_PRE_TEST)->firstOrFail();
        $topics = Topic::query()->orderBy('order')->get();
        // Jumlah jawaban benar per topik (dari 5 soal): 100%, 80%, 60%, 40%, 0%.
        $correctPerTopic = array_combine($topics->pluck('id')->all(), [5, 4, 3, 2, 0]);

        $answers = [];
        foreach ($preTest->questions()->with('options')->get()->groupBy('topic_id') as $topicId => $questions) {
            foreach ($questions->values() as $i => $question) {
                $answers[$question->id] = $question->options->firstWhere('is_correct', $i < $correctPerTopic[$topicId])->id;
            }
        }

        $this->post(route('pre-test.submit'), ['answers' => $answers]);

        $levels = $this->mahasiswa->abilityAssessments()->orderBy('topic_id')->pluck('level')->all();
        // ≥80% sudah dikuasai, 60–79% perlu penguatan, <60% perlu dipelajari.
        $this->assertSame(['tinggi', 'tinggi', 'sedang', 'rendah', 'rendah'], $levels);

        // Topik yang sudah dikuasai tidak menjadi prioritas; prioritas dari nilai terendah.
        $recommendations = $this->mahasiswa->recommendations()->orderBy('priority')->get();
        $this->assertSame([$topics[4]->id, $topics[3]->id, $topics[2]->id], $recommendations->pluck('topic_id')->all());
        $this->assertStringStartsWith('Perlu dipelajari', $recommendations[0]->reason);
        $this->assertStringStartsWith('Perlu penguatan', $recommendations[2]->reason);

        $this->get(route('pre-test.result'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('criteria.0.label', 'Perlu dipelajari')
                ->where('criteria.1.label', 'Perlu penguatan')
                ->where('criteria.2.label', 'Sudah dikuasai')
                ->where('topics.1.level', 'tinggi')
                ->where('topics.1.recommended', false)
                ->where('topics.2.level', 'sedang')
                ->where('topics.2.recommended', true)
                ->where('topics.4.start_material.slug', $recommendations[0]->material->slug)
                ->whereNot('topics.0.start_material', null));

        $this->get(route('materi.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('topics.0.category', 'rendah')
                ->where('topics.0.title', $topics[4]->title)
                ->where('topics.2.category', 'sedang')
                ->where('topics.3.category', 'tinggi'));
    }
}
