<?php

namespace Database\Factories;

use App\Models\Tache;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tache>
 */
class TacheFactory extends Factory
{
    protected $model = Tache::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = Carbon::now();
        $deadline = $start->copy()->addDays(3);

        return [
            'id_projet' => null,
            'id_epic' => null,
            'id_sprint' => null,
            'id_utilisateur' => null,
            'titre' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'start_date' => $start->toDateString(),
            'deadline' => $deadline->toDateString(),
            'status' => 'todo',
            'attachment_path' => null,
        ];
    }

    public function checkProxima(): static
    {
        $start = now();
        $deadline = now()->addDays(3);

        return $this->state(function () use ($start, $deadline) {
            return [
                'titre' => 'Check PROXIMA functionality',
                'description' => 'Go through the main features of PROXIMA and verify that task, sprint and project management works as expected.',
                'start_date' => $start->toDateString(),
                'deadline' => $deadline->toDateString(),
                'status' => 'in_progress',
            ];
        });
    }
}
