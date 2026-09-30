<?php

namespace Database\Seeders;

use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\User;
use App\Services\PreTestGrader;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Random\Engine\Mt19937;
use Random\Randomizer;

/**
 * Data sementara: mahasiswa contoh beserta hasil pre-test untuk uji coba dashboard admin.
 * Semua akun memakai email @demo.ipcara.test dan password "password".
 */
class DemoSeeder extends Seeder
{
    public function run(PreTestGrader $grader): void
    {
        $preTest = Test::query()->where('kind', Test::KIND_PRE_TEST)->first();

        if (! $preTest) {
            return;
        }

        $questions = $preTest->questions()->with('options:id,question_id,is_correct')->get();

        $students = [
            ['Aisyah Putri', '2024101', 0.92],
            ['Budi Santoso', '2024102', 0.46],
            ['Citra Lestari', '2024103', 0.74],
            ['Dimas Pratama', '2024104', 0.58],
            ['Eka Rahmawati', '2024105', 0.84],
            ['Fajar Nugroho', '2024106', 0.36],
            ['Gita Maharani', '2024107', 0.68],
            ['Hendra Wijaya', '2024108', 0.52],
        ];

        foreach ($students as $i => [$name, $nim, $accuracy]) {
            $user = User::query()->firstOrCreate(
                ['email' => 'demo'.($i + 1).'@demo.ipcara.test'],
                [
                    'name' => $name,
                    'username' => Str::slug($name, '_'),
                    'password' => Hash::make('password'),
                    'role' => 'mahasiswa',
                    'nim' => $nim,
                    'semester' => 3,
                    'program_studi' => 'Keperawatan',
                    'email_verified_at' => now(),
                ],
            );

            if ($user->testAttempts()->where('test_id', $preTest->id)->exists()) {
                continue;
            }

            $random = new Randomizer(new Mt19937(crc32($nim)));
            $finishedAt = now()->subDays(8 - $i)->setTime(9 + $i, 15);

            $attempt = TestAttempt::query()->create([
                'user_id' => $user->id,
                'test_id' => $preTest->id,
                'attempt_number' => 1,
                'status' => 'in_progress',
                'total_questions' => $questions->count(),
                'started_at' => $finishedAt->copy()->subMinutes(25 + $i * 3),
            ]);

            $answers = $questions->mapWithKeys(function ($question) use ($random, $accuracy) {
                $correct = $random->getFloat(0, 1) < $accuracy;
                $pool = $question->options->where('is_correct', $correct)->values();

                return [$question->id => $pool[$random->getInt(0, $pool->count() - 1)]->id];
            });

            $grader->grade($attempt, $answers, $finishedAt);
        }
    }
}
