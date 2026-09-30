<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@ipcara.test',
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Mahasiswa Contoh',
            'username' => 'mahasiswa',
            'email' => 'mahasiswa@ipcara.test',
            'role' => 'mahasiswa',
            'nim' => '2024001',
            'semester' => 3,
            'program_studi' => 'Keperawatan',
        ]);

        $this->call([PpiSeeder::class, PostTestSeeder::class, PracticeSeeder::class, DemoSeeder::class]);
    }
}
