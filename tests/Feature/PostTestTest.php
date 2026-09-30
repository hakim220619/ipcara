<?php

namespace Tests\Feature;

use App\Models\Material;
use App\Models\MaterialProgress;
use App\Models\Question;
use App\Models\Test as TestModel;
use App\Models\TestAttempt;
use App\Models\User;
use App\Services\PreTestGrader;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PostTestTest extends TestCase
{
    use RefreshDatabase;

    private User $mahasiswa;

    private TestModel $postTest;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
        $this->mahasiswa = User::query()->where('email', 'mahasiswa@ipcara.test')->firstOrFail();
        $this->postTest = TestModel::query()->where('kind', TestModel::KIND_POST_TEST)->firstOrFail();

        $preTest = TestModel::query()->where('kind', TestModel::KIND_PRE_TEST)->firstOrFail();
        $attempt = TestAttempt::query()->create([
            'user_id' => $this->mahasiswa->id,
            'test_id' => $preTest->id,
            'started_at' => now()->subHour(),
        ]);
        app(PreTestGrader::class)->grade($attempt, collect());
    }

    private function completeAllMaterials(): void
    {
        Material::query()->where('is_published', true)->pluck('id')->each(fn ($id) => MaterialProgress::query()->updateOrCreate(
            ['user_id' => $this->mahasiswa->id, 'material_id' => $id],
            ['status' => 'selesai', 'mastery_status' => 'mastery', 'progress_percent' => 100, 'completed_at' => now()],
        ));
    }

    #[Test]
    public function post_test_berisi_25_soal_yang_berbeda_dari_pre_test(): void
    {
        $preTest = TestModel::query()->where('kind', TestModel::KIND_PRE_TEST)->firstOrFail();

        $this->assertSame(25, $this->postTest->questions()->count());
        $this->assertSame(5, $this->postTest->questions()->where('topic_id', $preTest->questions()->first()->topic_id)->count());
        $this->assertSame(0, $this->postTest->questions()->whereIn('questions.id', $preTest->questions()->pluck('questions.id'))->count());
    }

    #[Test]
    public function post_test_terkunci_sebelum_semua_materi_selesai(): void
    {
        $this->actingAs($this->mahasiswa)
            ->get(route('post-test.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('post-test/Index')
                ->where('unlocked', false)
                ->where('test.question_count', 25));

        $this->post(route('post-test.start'))->assertRedirect(route('post-test.index'));
        $this->assertSame(0, TestAttempt::query()->where('test_id', $this->postTest->id)->count());
    }

    #[Test]
    public function setelah_materi_selesai_post_test_dapat_dikerjakan_dan_dibandingkan_dengan_pre_test(): void
    {
        $this->completeAllMaterials();

        $this->actingAs($this->mahasiswa)
            ->get(route('materi.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('postTest.unlocked', true)
                ->where('postTest.done', false)
                ->etc());

        $this->post(route('post-test.start'))->assertRedirect(route('post-test.take'));
        $this->get(route('post-test.take'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('post-test/Take')
                ->has('questions', 25)
                ->missing('questions.0.options.0.is_correct'));

        $answers = Question::query()
            ->whereIn('id', $this->postTest->questions()->pluck('questions.id'))
            ->with('correctOption')
            ->get()
            ->mapWithKeys(fn (Question $q) => [$q->id => $q->correctOption->id]);

        $this->post(route('post-test.submit'), ['answers' => $answers->all()])
            ->assertRedirect(route('post-test.result'));

        $attempt = TestAttempt::query()->where('test_id', $this->postTest->id)->firstOrFail();
        $this->assertSame('completed', $attempt->status);
        $this->assertSame(25, $attempt->correct_count);
        $this->assertEquals(100.0, (float) $attempt->score);
        $this->assertTrue($attempt->is_passed);

        $this->get(route('post-test.result'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('post-test/Result')
                ->where('result.percent', 100)
                ->where('result.pre_percent', 0)
                ->has('topics', 5)
                ->where('topics.0.pre_score', 0)
                ->where('topics.0.score', 100));

        $this->get(route('post-test.index'))->assertRedirect(route('post-test.result'));
        $this->post(route('post-test.start'))->assertRedirect(route('post-test.result'));
    }

    #[Test]
    public function admin_dapat_mengelola_soal_post_test(): void
    {
        $admin = User::query()->where('role', 'admin')->firstOrFail();
        $topicId = $this->postTest->questions()->first()->topic_id;

        $this->actingAs($admin)
            ->get(route('admin.questions.index', ['tes' => 'post_test']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('questions.total', 25)
                ->where('postTestCount', 25)
                ->where('questions.data.0.in_post_test', true)
                ->where('questions.data.0.in_pre_test', false));

        $this->post(route('admin.questions.store'), [
            'topic_id' => $topicId,
            'difficulty' => 'sedang',
            'question_text' => 'Soal post-test baru',
            'options' => ['A', 'B', 'C', 'D'],
            'correct_option' => 2,
            'is_active' => true,
            'in_pre_test' => false,
            'in_post_test' => true,
            'tes' => 'post_test',
        ])->assertRedirect(route('admin.questions.index', ['tes' => 'post_test']));

        $question = Question::query()->where('question_text', 'Soal post-test baru')->firstOrFail();
        $this->assertSame(26, $this->postTest->questions()->count());
        $this->assertFalse(TestModel::query()->where('kind', TestModel::KIND_PRE_TEST)->first()->questions()->whereKey($question->id)->exists());

        $this->put(route('admin.questions.update', $question), [
            'topic_id' => $topicId,
            'difficulty' => 'sedang',
            'question_text' => 'Soal post-test baru',
            'options' => ['A', 'B', 'C', 'D'],
            'correct_option' => 2,
            'is_active' => true,
            'in_pre_test' => false,
            'in_post_test' => false,
        ])->assertRedirect(route('admin.questions.index'));

        $this->assertSame(25, $this->postTest->questions()->count());

        $this->delete(route('admin.questions.destroy', $question))->assertRedirect();
        $this->assertModelMissing($question);
    }
}
