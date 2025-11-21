<?php

namespace Database\Factories;

use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Utilisateur>
 */
class UtilisateurFactory extends Factory
{
    protected $model = Utilisateur::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = \Faker\Factory::create();

        return [
            'nom'    => $faker->lastName(),
            'prenom' => $faker->firstName(),
            'email'  => $faker->unique()->safeEmail(),
            'password' => bcrypt('password'),
        ];
    }
}
