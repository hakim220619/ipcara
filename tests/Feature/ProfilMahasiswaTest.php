<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProfilMahasiswaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    #[Test]
    public function registrasi_menyimpan_nama_nim_semester_username_email(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Rina Kartika',
            'username' => 'rina_k',
            'email' => 'Rina@Kampus.ac.id',
            'nim' => '2025010',
            'semester' => 1,
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasNoErrors();

        $user = User::query()->where('username', 'rina_k')->firstOrFail();
        $this->assertSame('rina@kampus.ac.id', $user->email);
        $this->assertSame('2025010', $user->nim);
        $this->assertSame(1, $user->semester);
        $this->assertTrue($user->isMahasiswa());
    }

    #[Test]
    public function registrasi_wajib_nim_semester_dan_username_unik(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Duplikat',
            'username' => 'mahasiswa',
            'email' => 'dup@kampus.ac.id',
            'nim' => '2024001',
            'semester' => 20,
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors(['username', 'nim', 'semester']);

        $this->post(route('register.store'), [
            'name' => 'Kosong',
            'email' => 'kosong@kampus.ac.id',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors(['username', 'nim', 'semester']);
    }

    #[Test]
    public function mahasiswa_dapat_masuk_dengan_username_atau_email(): void
    {
        $this->post(route('login.store'), ['email' => 'mahasiswa', 'password' => 'password'])
            ->assertSessionHasNoErrors();
        $this->assertAuthenticated();

        auth()->logout();

        $this->post(route('login.store'), ['email' => 'MAHASISWA@ipcara.test', 'password' => 'password'])
            ->assertSessionHasNoErrors();
        $this->assertAuthenticated();

        auth()->logout();

        $this->post(route('login.store'), ['email' => 'mahasiswa', 'password' => 'salah']);
        $this->assertGuest();
    }

    #[Test]
    public function dashboard_menampilkan_data_profil_mahasiswa(): void
    {
        $user = User::query()->where('username', 'aisyah_putri')->firstOrFail();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard')
                ->where('auth.user.name', 'Aisyah Putri')
                ->where('auth.user.nim', '2024101')
                ->where('auth.user.semester', 3)
                ->where('auth.user.username', 'aisyah_putri')
                ->where('auth.user.email', 'demo1@demo.ipcara.test')
                ->missing('auth.user.password'));
    }

    #[Test]
    public function mahasiswa_dapat_mengubah_username_dan_semester_tetapi_bukan_nim(): void
    {
        $user = User::query()->where('username', 'aisyah_putri')->firstOrFail();

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => 'Aisyah Putri',
                'username' => 'aisyah',
                'email' => 'demo1@demo.ipcara.test',
                'semester' => 4,
                'nim' => '9999999',
            ])
            ->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertSame('aisyah', $user->username);
        $this->assertSame(4, $user->semester);
        $this->assertSame('2024101', $user->nim);

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => 'Aisyah Putri',
                'username' => 'budi_santoso',
                'email' => 'demo1@demo.ipcara.test',
            ])
            ->assertSessionHasErrors(['username', 'semester']);
    }
}
