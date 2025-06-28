<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Kyc;

class KycFactory extends Factory
{
    protected $model = Kyc::class;

    public function definition(): array
    {
        return [
            'user_id' => null, // à remplir dans le seeder
            'client_id' => null,
            'numero_npi' => (string) fake()->unique()->numberBetween(1000000000, 9999999999),
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'nationalite' => fake()->country(),
            'telephone' => fake()->phoneNumber(),
            'adresse_postale' => fake()->address(),
            'status' => 'EnCours',
        ];
    }
}
