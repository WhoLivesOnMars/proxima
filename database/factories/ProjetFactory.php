<?php

namespace Database\Factories;

use App\Models\Projet;
use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjetFactory extends Factory
{
    protected $model = Projet::class;

    public function definition(): array
    {
        $faker = \Faker\Factory::create();

        return [
            'nom'         => $faker->sentence(3),
            'description' => $faker->paragraph(),
            'owner_id'    => Utilisateur::factory(),
            'status'      => 'active',
            'visibility'  => 'private',
            'share_token' => null,
        ];
    }

    public function onboarding(): static
    {
        return $this->state(function () {
            return [
                'nom'         => 'First Project',
                'description' => 'Starter project created automatically to explore PROXIMA.',
                'status'      => 'active',
                'visibility'  => 'private',
            ];
        });
    }
}
