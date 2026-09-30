<?php

namespace Tests\Feature;

use App\Models\Material;
use App\Models\MaterialPracticeAnswer;
use App\Models\MaterialPracticeAttempt;
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

class MaterialPracticeTest extends TestCase
{
    use RefreshDatabase;

    private User $mahasiswa;

    private Material $material;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
        $this->mahasiswa = User::query()->where('email', 'mahasiswa@ipcara.test')->firstOrFail();
        $this->material = Material::query()->orderBy('topic_id')->orderBy('order')->firstOrFail();

        $preTest = TestModel::query()->where('kind', TestModel::KIND_PRE_TEST)->firstOrFail();
        $attempt = TestAttempt::query()->create([
            'user_id' => $this->mahasiswa->id,
            'test_id' => $preTest->id,
            'started_at' => now()->subHour(),
        ]);
        app(PreTestGrader::class)->grade($attempt, collect());
    }

    private function currentAttempt(): MaterialPracticeAttempt
    {
        return MaterialPracticeAttempt::query()->where('user_id', $this->mahasiswa->id)->latest('id')->firstOrFail();
    }

    /**
     * Jawab soal ronde berjalan; $correct = jumlah soal (dari awal) yang dijawab benar.
     */
    private function answerRound(int $correct): MaterialPracticeAttempt
    {
        $attempt = $this->currentAttempt();

        foreach ($attempt->question_ids as $i => $questionId) {
            $options = Question::query()->findOrFail($questionId)->options;
            $option = $i < $correct ? $options->firstWhere('is_correct', true) : $options->firstWhere('is_correct', false);

            $this->post(route('materi.latihan.answer', $this->material), [
                'question_id' => $questionId,
                'option_id' => $option->id,
                'time_spent' => 12,
            ]);
        }

        $this->post(route('materi.latihan.finish', $this->material))
            ->assertRedirect(route('materi.latihan.result', [$this->material, $attempt]));

        return $attempt->refresh();
    }

    private function startNext(): MaterialPracticeAttempt
    {
        $this->post(route('materi.latihan.start', $this->material))->assertRedirect(route('materi.latihan.take', $this->material));

        return $this->currentAttempt();
    }

    #[Test]
    public function setiap_materi_memiliki_3_indikator_dengan_soal_utama_penguatan_dan_remedial(): void
    {
        $this->assertSame(0, Material::query()->whereDoesntHave('indicators')->count());

        foreach (Material::query()->with('indicators.questions')->get() as $material) {
            $this->assertCount(3, $material->indicators);

            foreach ($material->indicators as $indicator) {
                $this->assertEqualsCanonicalizing(['utama', 'penguatan', 'remedial'], $indicator->questions->pluck('practice_role')->all());
                // Varian soal tidak sama persis.
                $this->assertCount(3, $indicator->questions->pluck('question_text')->unique());
            }
        }
    }

    #[Test]
    public function menyelesaikan_materi_langsung_menampilkan_3_soal_latihan_dengan_umpan_balik(): void
    {
        $this->actingAs($this->mahasiswa)
            ->post(route('materi.complete', $this->material))
            ->assertRedirect(route('materi.latihan.take', $this->material));

        $attempt = $this->currentAttempt();
        $this->assertSame('utama', $attempt->round);
        $this->assertCount(3, $attempt->question_ids);

        $this->get(route('materi.latihan.take', $this->material))
            ->assertInertia(fn (Assert $page) => $page
                ->component('materi/latihan/Take')
                ->has('questions', 3)
                ->where('questions.0.feedback', null)
                ->missing('questions.0.options.0.is_correct'));

        $questionId = $attempt->question_ids[0];
        $wrong = Question::query()->findOrFail($questionId)->options->firstWhere('is_correct', false);
        $this->post(route('materi.latihan.answer', $this->material), ['question_id' => $questionId, 'option_id' => $wrong->id, 'time_spent' => 20]);

        $this->get(route('materi.latihan.take', $this->material))
            ->assertInertia(fn (Assert $page) => $page
                ->where('questions.0.feedback.is_correct', false)
                ->where('questions.0.feedback.chosen_option_id', $wrong->id)
                ->whereNot('questions.0.feedback.explanation', null));

        // Data yang disimpan: mahasiswa, materi, soal, jawaban, benar/salah, waktu.
        $this->assertDatabaseHas('material_practice_answers', [
            'user_id' => $this->mahasiswa->id,
            'material_id' => $this->material->id,
            'question_id' => $questionId,
            'question_option_id' => $wrong->id,
            'is_correct' => false,
            'time_spent_seconds' => 20,
        ]);

        // Belum semua dijawab: skor belum dihitung.
        $this->post(route('materi.latihan.finish', $this->material))->assertRedirect(route('materi.latihan.take', $this->material));
        $this->assertSame('in_progress', $attempt->refresh()->status);
    }

    #[Test]
    public function tiga_benar_mencapai_mastery_dan_lanjut_ke_materi_berikutnya(): void
    {
        $this->actingAs($this->mahasiswa)->post(route('materi.complete', $this->material));
        $attempt = $this->answerRound(3);

        $this->assertSame('mastery', $attempt->outcome);
        $this->assertEquals(100.0, (float) $attempt->score);
        $this->assertNotNull($attempt->duration_seconds);

        $progress = MaterialProgress::query()->where('user_id', $this->mahasiswa->id)->where('material_id', $this->material->id)->firstOrFail();
        $this->assertSame('mastery', $progress->mastery_status);
        $this->assertSame(1, $progress->practice_attempts);
        $this->assertNotNull($progress->mastered_at);

        $next = Material::query()->where('topic_id', $this->material->topic_id)->where('order', 2)->firstOrFail();
        $this->get(route('materi.latihan.result', [$this->material, $attempt]))
            ->assertInertia(fn (Assert $page) => $page
                ->component('materi/latihan/Result')
                ->where('result.outcome', 'mastery')
                ->where('next.type', 'materi')
                ->where('next.slug', $next->slug));

        // Materi yang sudah mastery tidak otomatis masuk latihan lagi.
        $this->post(route('materi.complete', $this->material))->assertRedirect();
        $this->assertSame(1, MaterialPracticeAttempt::query()->count());
    }

    #[Test]
    public function dua_benar_memberi_materi_penguatan_dan_2_soal_penguatan_yang_berbeda(): void
    {
        $this->actingAs($this->mahasiswa)->post(route('materi.complete', $this->material));
        $utama = $this->answerRound(2);

        $this->assertSame('penguatan', $utama->outcome);
        $missedQuestion = Question::query()->findOrFail($utama->question_ids[2]);

        $this->get(route('materi.latihan.result', [$this->material, $utama]))
            ->assertInertia(fn (Assert $page) => $page
                ->where('result.outcome', 'penguatan')
                ->has('focus', 1)
                ->where('focus.0.description', $missedQuestion->indicator->description)
                ->whereNot('focus.0.reinforcement', null));

        $penguatan = $this->startNext();
        $this->assertSame('penguatan', $penguatan->round);
        $this->assertCount(2, $penguatan->question_ids);
        $this->assertEmpty(array_intersect($penguatan->question_ids, $utama->question_ids));

        // Soal penguatan pertama menguji indikator yang belum dikuasai.
        $first = Question::query()->findOrFail($penguatan->question_ids[0]);
        $this->assertSame($missedQuestion->indicator_id, $first->indicator_id);
        $this->assertSame('penguatan', $first->practice_role);

        $this->assertSame('mastery', $this->answerRound(2)->outcome);
        $this->assertSame(2, $penguatan->refresh()->attempt_number);
    }

    #[Test]
    public function nol_atau_satu_benar_mengarahkan_ke_bagian_materi_dan_3_soal_remedial(): void
    {
        $this->actingAs($this->mahasiswa)->post(route('materi.complete', $this->material));
        $utama = $this->answerRound(1);

        $this->assertSame('remedial', $utama->outcome);
        $this->assertSame('remedial', MaterialProgress::query()->where('material_id', $this->material->id)->value('mastery_status'));

        $this->get(route('materi.latihan.result', [$this->material, $utama]))
            ->assertInertia(fn (Assert $page) => $page
                ->where('result.outcome', 'remedial')
                ->has('focus', 2)
                ->whereNot('focus.0.anchor', null));

        $remedial = $this->startNext();
        $this->assertSame('remedial', $remedial->round);
        $this->assertCount(3, $remedial->question_ids);
        $this->assertEmpty(array_intersect($remedial->question_ids, $utama->question_ids));
        $this->assertSame(
            Question::query()->whereIn('id', $utama->question_ids)->pluck('indicator_id')->sort()->values()->all(),
            Question::query()->whereIn('id', $remedial->question_ids)->pluck('indicator_id')->sort()->values()->all(),
        );

        // Remedial 2/3 → penguatan; remedial ≤1/3 → baca ulang lalu latihan utama.
        $this->assertSame('penguatan', $this->answerRound(2)->outcome);
        $this->assertSame('remedial', $this->startNext()->round === 'penguatan' ? $this->answerRound(0)->outcome : null);
        $this->startNext();
        $this->assertSame('ulang_materi', $this->answerRound(0)->outcome);
        $this->assertSame('utama', $this->startNext()->round);
    }

    #[Test]
    public function post_test_terkunci_sampai_semua_materi_mastery_dan_latihan_tidak_mengubah_post_test(): void
    {
        $this->actingAs($this->mahasiswa);

        Material::query()->pluck('id')->each(fn ($id) => MaterialProgress::query()->updateOrCreate(
            ['user_id' => $this->mahasiswa->id, 'material_id' => $id],
            ['status' => 'selesai', 'progress_percent' => 100, 'completed_at' => now()],
        ));
        $this->assertFalse($this->mahasiswa->canTakePostTest());

        MaterialProgress::query()->update(['mastery_status' => 'mastery']);
        $this->assertTrue($this->mahasiswa->canTakePostTest());

        $this->post(route('post-test.start'))->assertRedirect(route('post-test.take'));
        $this->post(route('post-test.submit'), ['answers' => []]);

        $postAttempt = TestAttempt::query()->whereHas('test', fn ($q) => $q->where('kind', TestModel::KIND_POST_TEST))->firstOrFail();
        $this->assertEquals(0.0, (float) $postAttempt->score);

        $this->post(route('materi.latihan.start', $this->material));
        $this->answerRound(3);
        $this->assertEquals(0.0, (float) $postAttempt->refresh()->score);

        $this->get(route('post-test.result'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('post-test/Result')
                ->has('topics', 5)
                ->has('criteria')
                ->where('result.pre_percent', 0)
                ->where('report.postTest.score', 0)
                ->where('report.practice.mastered', 15)
                ->where('report.practice.attempts', 1)
                ->has('report.practice.materials', 15)
                ->has('report.topics', 5)
                ->where('report.conclusion.verdict', 'Perlu Peningkatan')
                ->where('report.conclusion.tone', 'low')
                ->where('report.conclusion.recommendation', fn ($text) => str_contains($text, 'Pelajari kembali topik'))
                ->has('report.conclusion.points'));
    }

    #[Test]
    public function hasil_latihan_mahasiswa_lain_tidak_dapat_dibuka(): void
    {
        $this->actingAs($this->mahasiswa)->post(route('materi.complete', $this->material));
        $attempt = $this->answerRound(3);

        $admin = User::query()->where('role', 'admin')->firstOrFail();
        $this->actingAs($admin)->get(route('materi.latihan.result', [$this->material, $attempt]))->assertNotFound();
        $this->assertSame(3, MaterialPracticeAnswer::query()->count());
    }

    #[Test]
    public function admin_dapat_menambah_soal_latihan_materi(): void
    {
        $admin = User::query()->where('role', 'admin')->firstOrFail();
        $indicator = $this->material->indicators()->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.questions.index', ['tes' => 'latihan']))
            ->assertInertia(fn (Assert $page) => $page->where('latihanCount', 135));

        $this->post(route('admin.questions.store'), [
            'topic_id' => $this->material->topic_id,
            'difficulty' => 'mudah',
            'question_text' => 'Soal latihan tambahan',
            'explanation' => 'Pembahasan.',
            'options' => ['A', 'B', 'C', 'D'],
            'correct_option' => 1,
            'is_active' => true,
            'in_pre_test' => false,
            'practice_role' => 'penguatan',
            'material_id' => $this->material->id,
            'indicator_id' => $indicator->id,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('questions', [
            'question_text' => 'Soal latihan tambahan',
            'material_id' => $this->material->id,
            'indicator_id' => $indicator->id,
            'practice_role' => 'penguatan',
        ]);

        // Indikator harus milik materi yang dipilih.
        $otherIndicator = Material::query()->whereKeyNot($this->material->id)->firstOrFail()->indicators()->firstOrFail();
        $this->post(route('admin.questions.store'), [
            'topic_id' => $this->material->topic_id,
            'difficulty' => 'mudah',
            'question_text' => 'Soal salah indikator',
            'options' => ['A', 'B', 'C', 'D'],
            'correct_option' => 1,
            'practice_role' => 'utama',
            'material_id' => $this->material->id,
            'indicator_id' => $otherIndicator->id,
        ])->assertSessionHasErrors('indicator_id');
    }
}
