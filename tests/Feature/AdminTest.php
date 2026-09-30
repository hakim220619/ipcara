<?php

namespace Tests\Feature;

use App\Models\AbilityAssessment;
use App\Models\Question;
use App\Models\ScoreCriterion;
use App\Models\Setting;
use App\Models\Test as TestModel;
use App\Models\TestAttempt;
use App\Models\Topic;
use App\Models\User;
use App\Support\Theme;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $mahasiswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
        $this->admin = User::query()->where('email', 'admin@ipcara.test')->firstOrFail();
        $this->mahasiswa = User::query()->where('email', 'mahasiswa@ipcara.test')->firstOrFail();
    }

    #[Test]
    public function hanya_ada_role_mahasiswa_dan_admin(): void
    {
        $this->assertEqualsCanonicalizing(['admin', 'mahasiswa'], User::query()->distinct()->pluck('role')->all());
        $this->assertSame(3, ScoreCriterion::query()->count());
    }

    #[Test]
    public function mahasiswa_tidak_dapat_membuka_halaman_admin(): void
    {
        $this->actingAs($this->mahasiswa);

        $this->get(route('admin.dashboard'))->assertForbidden();
        $this->get(route('admin.questions.index'))->assertForbidden();
        $this->post(route('admin.criteria.store'), [])->assertForbidden();
        $this->delete(route('admin.scores.destroy', 1))->assertForbidden();
    }

    #[Test]
    public function admin_diarahkan_ke_dashboard_admin(): void
    {
        $this->actingAs($this->admin)->get(route('dashboard'))->assertRedirect(route('admin.dashboard'));

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('admin/Dashboard')
                ->where('stats.questions', 235)
                ->where('stats.completed', 8)
                ->has('distribution', 3)
                ->has('topicAverages', 5)
                ->where('preTestDone', true));
    }

    #[Test]
    public function admin_dapat_menambah_mengubah_dan_menghapus_soal(): void
    {
        $this->actingAs($this->admin);
        $topic = Topic::query()->where('title', 'Etika Batuk')->firstOrFail();
        $preTest = TestModel::query()->where('kind', TestModel::KIND_PRE_TEST)->firstOrFail();

        $this->get(route('admin.questions.index', ['topic' => $topic->id]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('admin/soal/Index')
                ->where('questions.total', 47)
                ->has('questions.data', 15));

        $payload = [
            'topic_id' => $topic->id,
            'difficulty' => 'mudah',
            'question_text' => 'Apa tujuan memakai masker saat batuk?',
            'explanation' => 'Menahan droplet.',
            'options' => ['Gaya', 'Menahan droplet', 'Agar hangat', 'Aturan'],
            'correct_option' => 1,
            'is_active' => true,
            'in_pre_test' => true,
        ];

        $this->post(route('admin.questions.store'), $payload)->assertRedirect(route('admin.questions.index'));

        $question = Question::query()->where('question_text', $payload['question_text'])->firstOrFail();
        $this->assertSame('B', $question->correctOption->label);
        $this->assertSame(26, $preTest->questions()->count());

        $this->get(route('admin.questions.edit', $question))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('question.correct_option', 1)
                ->where('question.in_pre_test', true)
                ->has('question.options', 4));

        $optionIds = $question->options()->pluck('id')->all();

        $this->put(route('admin.questions.update', $question), [
            ...$payload,
            'question_text' => 'Tujuan utama memakai masker saat batuk adalah ...',
            'correct_option' => 3,
            'in_pre_test' => false,
        ])->assertRedirect(route('admin.questions.index'));

        $question->refresh();
        $this->assertSame('Tujuan utama memakai masker saat batuk adalah ...', $question->question_text);
        $this->assertSame('D', $question->correctOption->label);
        $this->assertSame($optionIds, $question->options()->pluck('id')->all());
        $this->assertSame(25, $preTest->questions()->count());

        $this->delete(route('admin.questions.destroy', $question))->assertRedirect();
        $this->assertModelMissing($question);
    }

    #[Test]
    public function validasi_soal_wajib_empat_pilihan_dan_kunci(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.questions.store'), [
                'topic_id' => 999,
                'difficulty' => 'mudah',
                'question_text' => '',
                'options' => ['A', '', 'C'],
                'correct_option' => 5,
            ])
            ->assertSessionHasErrors(['topic_id', 'question_text', 'options', 'options.1', 'correct_option']);
    }

    #[Test]
    public function kriteria_penilaian_mempengaruhi_klasifikasi(): void
    {
        $this->actingAs($this->admin);
        $sedang = ScoreCriterion::query()->where('level', 'sedang')->firstOrFail();
        $tinggi = ScoreCriterion::query()->where('level', 'tinggi')->firstOrFail();

        $this->put(route('admin.criteria.update', $sedang), [
            'level' => 'sedang', 'label' => 'Cukup', 'min_score' => 60, 'max_score' => 89.99,
            'recommended_material_level' => 'menengah', 'is_recommended' => true,
        ])->assertSessionHasErrors('min_score');

        $this->put(route('admin.criteria.update', $tinggi), [
            'level' => 'tinggi', 'label' => 'Tinggi', 'min_score' => 90, 'max_score' => 100,
            'recommended_material_level' => 'lanjut', 'is_recommended' => false,
        ])->assertSessionHasNoErrors();

        $this->put(route('admin.criteria.update', $sedang), [
            'level' => 'sedang', 'label' => 'Cukup', 'min_score' => 60, 'max_score' => 89.99,
            'recommended_material_level' => 'menengah', 'is_recommended' => true,
        ])->assertSessionHasNoErrors();

        $this->assertSame('sedang', AbilityAssessment::classify(85)['level']);
        $this->assertTrue(AbilityAssessment::classify(85)['recommended']);
        $this->assertSame('tinggi', AbilityAssessment::classify(95)['level']);

        $this->post(route('admin.criteria.store'), [
            'level' => 'rendah', 'label' => 'Duplikat', 'min_score' => 0, 'max_score' => 10,
            'recommended_material_level' => 'dasar',
        ])->assertSessionHasErrors('level');

        $this->delete(route('admin.criteria.destroy', $sedang))->assertRedirect();
        $this->assertSame(2, ScoreCriterion::query()->count());

        $this->put(route('admin.settings.update'), ['duration_minutes' => 45, 'passing_score' => 75])
            ->assertSessionHasNoErrors();
        $this->assertSame(75, TestModel::query()->where('kind', TestModel::KIND_PRE_TEST)->value('passing_score'));
    }

    #[Test]
    public function admin_dapat_mengelola_nilai_mahasiswa(): void
    {
        $this->actingAs($this->admin);
        $topics = Topic::query()->orderBy('order')->pluck('id');

        $this->get(route('admin.scores.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('admin/nilai/Index')
                ->where('attempts.total', 8)
                ->has('attempts.data.0.topic_scores', 5)
                ->where('attempts.data.0.max_points', 500)
                ->has('students', 1)
                ->has('topics', 5));

        $scores = array_combine($topics->all(), [90, 60, 80, 70, 100]);

        $this->post(route('admin.scores.store'), [
            'user_id' => $this->mahasiswa->id,
            'topic_scores' => $scores,
            'finished_at' => '2026-09-20 10:00',
        ])->assertSessionHasNoErrors();

        $attempt = TestAttempt::query()->where('user_id', $this->mahasiswa->id)->firstOrFail();
        $summary = $attempt->load('abilityAssessments.topic')->scoreSummary();
        $this->assertEquals(400, $summary['total_points']);
        $this->assertSame(500, $summary['max_points']);
        $this->assertEquals(80.0, (float) $attempt->score);
        $this->assertTrue($attempt->is_passed);
        $this->assertSame(21, $attempt->correct_count);
        $this->assertSame(25, $attempt->total_questions);
        $this->assertSame(2, $this->mahasiswa->recommendations()->count());
        $this->assertFalse($this->mahasiswa->fresh()->needsPreTest());

        $this->post(route('admin.scores.store'), [
            'user_id' => $this->mahasiswa->id,
            'topic_scores' => $scores,
            'finished_at' => '2026-09-20 10:00',
        ])->assertSessionHasErrors('user_id');

        $this->put(route('admin.scores.update', $attempt), [
            'topic_scores' => [...$scores, $topics[0] => 120],
            'finished_at' => '2026-09-20 10:00',
        ])->assertSessionHasErrors('topic_scores.'.$topics[0]);

        $this->put(route('admin.scores.update', $attempt), [
            'topic_scores' => array_slice($scores, 0, 3, true),
            'finished_at' => '2026-09-20 10:00',
        ])->assertSessionHasErrors('topic_scores');

        $this->put(route('admin.scores.update', $attempt), [
            'topic_scores' => array_combine($topics->all(), [50, 40, 60, 30, 70]),
            'finished_at' => '2026-09-21 11:00',
        ])->assertSessionHasNoErrors();

        $attempt->refresh();
        $this->assertEquals(50.0, (float) $attempt->score);
        $this->assertFalse($attempt->is_passed);
        $this->assertSame(5, $attempt->abilityAssessments()->count());
        $this->assertSame(5, $this->mahasiswa->recommendations()->count());

        $demo = TestAttempt::query()->where('user_id', '!=', $this->mahasiswa->id)->firstOrFail();
        $this->assertGreaterThan(0, $demo->abilityAssessments()->count());

        $this->delete(route('admin.scores.destroy', $demo))->assertRedirect();
        $this->assertModelMissing($demo);
        $this->assertSame(0, AbilityAssessment::query()->where('user_id', $demo->user_id)->count());
        $this->assertSame(0, DB::table('recommendations')->where('user_id', $demo->user_id)->count());
        $this->assertTrue($demo->user->needsPreTest());
    }

    #[Test]
    public function admin_dapat_mengelola_topik(): void
    {
        $this->actingAs($this->admin);

        $this->get(route('admin.topics.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('admin/topik/Index')
                ->has('topics', 5)
                ->where('topics.0.title', 'Pengertian PPI')
                ->where('topics.0.materials_count', 3)
                ->where('topics.0.questions_count', 47)
                ->where('nextOrder', 6));

        $this->post(route('admin.topics.store'), [
            'code' => 'PPI-06',
            'title' => 'Sterilisasi Alat',
            'description' => 'Metode sterilisasi dan disinfeksi.',
            'order' => 6,
            'is_published' => false,
        ])->assertSessionHasNoErrors();

        $topic = Topic::query()->where('code', 'PPI-06')->firstOrFail();
        $this->assertSame('sterilisasi-alat', $topic->slug);
        $this->assertFalse($topic->is_published);

        $this->post(route('admin.topics.store'), [
            'code' => 'PPI-06', 'title' => 'Duplikat', 'order' => 7,
        ])->assertSessionHasErrors('code');

        $this->put(route('admin.topics.update', $topic), [
            'code' => 'PPI-06',
            'title' => 'Sterilisasi dan Disinfeksi',
            'order' => 6,
            'is_published' => true,
        ])->assertSessionHasNoErrors();

        $topic->refresh();
        $this->assertSame('sterilisasi-dan-disinfeksi', $topic->slug);
        $this->assertTrue($topic->is_published);

        $etika = Topic::query()->where('title', 'Etika Batuk')->firstOrFail();
        $this->delete(route('admin.topics.destroy', $etika))->assertRedirect();
        $this->assertModelMissing($etika);
        $this->assertSame(0, Question::query()->where('topic_id', $etika->id)->count());
        $this->assertSame(20, TestModel::query()->where('kind', TestModel::KIND_PRE_TEST)->firstOrFail()->questions()->count());

        $this->actingAs($this->mahasiswa)->get(route('admin.topics.index'))->assertForbidden();
    }

    #[Test]
    public function admin_dapat_mengelola_pengguna_semua_role(): void
    {
        $this->actingAs($this->admin);

        $this->get(route('admin.users.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('admin/pengguna/Index')
                ->where('counts.all', 10)
                ->where('counts.admin', 1)
                ->where('counts.mahasiswa', 9)
                ->where('users.data.0.role', 'admin'));

        $this->get(route('admin.users.index', ['role' => 'admin']))
            ->assertInertia(fn (Assert $page) => $page->where('users.total', 1));

        $this->post(route('admin.users.store'), [
            'name' => 'Admin Kedua',
            'email' => 'Admin2@ipcara.test',
            'role' => 'admin',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertSessionHasNoErrors();

        $this->post(route('admin.users.store'), [
            'name' => 'Mahasiswa Baru',
            'email' => 'baru@ipcara.test',
            'role' => 'mahasiswa',
            'nim' => '2024001',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertSessionHasErrors('nim');

        $admin2 = User::query()->where('email', 'admin2@ipcara.test')->firstOrFail();
        $this->assertTrue($admin2->isAdmin());
        $this->assertNotNull($admin2->email_verified_at);

        $oldPassword = $this->mahasiswa->password;
        $this->put(route('admin.users.update', $this->mahasiswa), [
            'name' => 'Mahasiswa Diubah',
            'email' => 'mahasiswa@ipcara.test',
            'role' => 'mahasiswa',
            'nim' => '2024999',
        ])->assertSessionHasNoErrors();

        $this->mahasiswa->refresh();
        $this->assertSame('Mahasiswa Diubah', $this->mahasiswa->name);
        $this->assertSame('2024999', $this->mahasiswa->nim);
        $this->assertSame($oldPassword, $this->mahasiswa->password);

        $this->put(route('admin.users.update', $this->admin), [
            'name' => 'Administrator',
            'email' => 'admin@ipcara.test',
            'role' => 'mahasiswa',
        ])->assertSessionHasErrors('role');

        $this->delete(route('admin.users.destroy', $this->admin))->assertRedirect();
        $this->assertModelExists($this->admin);

        $this->delete(route('admin.users.destroy', $admin2))->assertRedirect();
        $this->assertModelMissing($admin2);

        $this->delete(route('admin.users.destroy', $this->mahasiswa))->assertRedirect();
        $this->assertModelMissing($this->mahasiswa);

        $this->actingAs(User::query()->where('role', 'mahasiswa')->firstOrFail())
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    #[Test]
    public function admin_dapat_mengatur_warna_tampilan(): void
    {
        $this->actingAs($this->admin);

        $this->get(route('admin.appearance.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('admin/tampilan/Index')
                ->where('colors.0.key', 'sidebar_background')
                ->where('colors.0.value', '#90caf9')
                ->where('themeCss', ''));

        $this->put(route('admin.appearance.update'), [
            'colors' => ['sidebar_background' => '#123abc', 'primary' => 'merah'],
        ])->assertSessionHasErrors('colors.primary');

        $this->put(route('admin.appearance.update'), [
            'colors' => ['sidebar_background' => '#1E88E5', 'primary' => '#74c0ec'],
        ])->assertSessionHasNoErrors();

        $this->assertSame('#1e88e5', Setting::query()->where('key', 'theme.sidebar_background')->value('value'));
        $this->assertFalse(Setting::query()->where('key', 'theme.primary')->exists());
        $this->assertSame(':root:root{--sidebar-background:#1e88e5;--sidebar:#1e88e5}', Theme::css());

        $this->get(route('admin.appearance.index'))
            ->assertSee('--sidebar-background:#1e88e5', false)
            ->assertInertia(fn (Assert $page) => $page
                ->where('colors.0.value', '#1e88e5')
                ->where('themeCss', ':root:root{--sidebar-background:#1e88e5;--sidebar:#1e88e5}'));

        $this->actingAs($this->mahasiswa)->get(route('pre-test.index'))
            ->assertSee('--sidebar-background:#1e88e5', false);

        $this->actingAs($this->admin)->put(route('admin.appearance.update'), [
            'colors' => ['sidebar_background' => '#90caf9'],
        ])->assertSessionHasNoErrors();
        $this->assertSame('', Theme::css());

        $this->actingAs($this->mahasiswa)->put(route('admin.appearance.update'), ['colors' => []])->assertForbidden();
    }
}
