<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Client;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'user_id' => null, // à remplir dynamiquement dans le seeder
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'statusActif' => fake()->randomElement(['Actif','Inactif']),
        ];
    }
}
