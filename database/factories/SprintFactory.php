<?php

namespace Database\Factories;

use App\Models\Sprint;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sprint>
 */
class SprintFactory extends Factory
{
    protected $model = Sprint::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = Carbon::now()->startOfWeek();

        return [
            'id_projet' => null,
            'nom' => 'Sprint '.$this->faker->numberBetween(1, 4),
            'start_date' => $start,
            'duree' => 2,
        ];
    }

    public function firstSprint(): static
    {
        return $this->state(function () {
            return [
                'nom' => 'Sprint 1',
                'start_date' => now(),
                'duree' => 2,
            ];
        });
    }
}
