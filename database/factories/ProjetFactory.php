<?php

namespace Database\Factories;

use App\Models\Projet;
use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Projet>
 */
class ProjetFactory extends Factory
{
    protected $model = Projet::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'owner_id' => Utilisateur::factory(),
            'status' => 'active',
            'visibility' => 'private',
            'share_token' => null,
        ];
    }

    public function onboarding(): static
    {
        return $this->state(function () {
            return [
                'nom' => 'First Project',
                'description' => 'Starter project created automatically to explore PROXIMA.',
                'status' => 'active',
                'visibility'  => 'private',
            ];
        });
    }
}
