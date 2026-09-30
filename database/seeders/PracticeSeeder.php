<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\Question;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Latihan per materi: 3 indikator kompetensi per materi, masing-masing dengan
 * soal utama, penguatan, dan remedial. Data soal ada di database/seeders/practice/{kode-topik}.php.
 */
class PracticeSeeder extends Seeder
{
    public function run(): void
    {
        $authorId = User::query()->where('role', 'admin')->value('id');

        foreach (glob(__DIR__.'/practice/*.php') as $file) {
            $topic = Topic::query()->where('code', strtoupper(basename($file, '.php')))->first();

            if (! $topic) {
                continue;
            }

            foreach (require $file as $materialTitle => $indicators) {
                $material = Material::query()->where('topic_id', $topic->id)->where('title', $materialTitle)->first();

                if (! $material || $material->indicators()->exists()) {
                    continue;
                }

                foreach ($indicators as $order => $data) {
                    $indicator = $material->indicators()->create([
                        'description' => $data['indicator'],
                        'section' => $data['section'] ?? null,
                        'reinforcement' => $data['reinforcement'] ?? null,
                        'order' => $order + 1,
                    ]);

                    foreach ($data['questions'] as $role => [$level, $text, $options, $answer, $explanation]) {
                        $question = Question::create([
                            'topic_id' => $topic->id,
                            'material_id' => $material->id,
                            'indicator_id' => $indicator->id,
                            'practice_role' => $role,
                            'type' => 'pilihan_ganda',
                            'difficulty' => $level,
                            'question_text' => $text,
                            'explanation' => $explanation,
                            'points' => 1,
                            'created_by' => $authorId,
                        ]);

                        foreach ($options as $idx => $optionText) {
                            $question->options()->create([
                                'label' => chr(65 + $idx),
                                'option_text' => $optionText,
                                'is_correct' => $idx === $answer,
                                'order' => $idx + 1,
                            ]);
                        }
                    }
                }
            }
        }
    }
}
