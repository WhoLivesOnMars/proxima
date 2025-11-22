<?php

namespace Database\Factories;

use App\Models\Epic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Epic>
 */
class EpicFactory extends Factory
{
    protected $model = Epic::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_projet' => null,
            'nom' => 'Epic '.$this->faker->numberBetween(1, 4),
        ];
    }

    public function firstEpic(): static
    {
        return $this->state(function () {
            return [
                'nom' => 'Epic 1',
            ];
        });
    }
}
