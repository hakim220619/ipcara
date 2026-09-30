<?php

namespace Tests\Feature;

use App\Models\Material;
use App\Models\MaterialProgress;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MateriTest extends TestCase
{
    use RefreshDatabase;

    private User $mahasiswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
        $this->mahasiswa = User::query()->where('email', 'mahasiswa@ipcara.test')->firstOrFail();

        $this->actingAs($this->mahasiswa)->post(route('pre-test.start'));
        $this->post(route('pre-test.submit'), ['answers' => []]);
        auth()->logout();
    }

    #[Test]
    public function dashboard_menampilkan_statistik_dan_modul_ppi(): void
    {
        $this->actingAs($this->mahasiswa)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard')
                ->has('stats', fn (Assert $stats) => $stats
                    ->where('preTestDone', true)
                    ->etc())
                ->has('moduleCompletion', 5)
                ->has('weeklyActivity', 7)
                ->has('timeline', 5)
                ->etc());
    }

    #[Test]
    public function daftar_materi_dikelompokkan_per_topik(): void
    {
        $this->actingAs($this->mahasiswa)
            ->get(route('materi.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('materi/Index')
                ->has('topics', 5)
                ->has('topics.0.materials', 3));
    }

    #[Test]
    public function membuka_materi_menandai_progres_dan_mencatat_aktivitas(): void
    {
        $material = Material::query()->firstOrFail();

        $this->actingAs($this->mahasiswa)
            ->get(route('materi.show', $material))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('materi/Show')
                ->where('material.id', $material->id)
                ->where('progress.status', 'sedang')
                ->missing('material.estimated_minutes')
                ->etc());

        $this->assertDatabaseHas('learning_activities', [
            'user_id' => $this->mahasiswa->id,
            'activity_type' => 'buka_materi',
            'subject_id' => $material->id,
        ]);
    }

    #[Test]
    public function menandai_materi_selesai_memperbarui_progres(): void
    {
        $material = Material::query()->firstOrFail();

        $this->actingAs($this->mahasiswa)
            ->post(route('materi.complete', $material))
            ->assertRedirect();

        $this->assertSame('selesai', MaterialProgress::query()
            ->where('user_id', $this->mahasiswa->id)
            ->where('material_id', $material->id)
            ->value('status'));
    }

    #[Test]
    public function tamu_diarahkan_ke_halaman_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('materi.index'))->assertRedirect(route('login'));
    }
}
