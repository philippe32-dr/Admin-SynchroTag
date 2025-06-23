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
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'nationalite' => fake()->country(),
            'telephone' => fake()->phoneNumber(),
            'adresse_postale' => fake()->address(),
            'pdf_cip' => 'dummy.pdf',
            'status' => fake()->randomElement(['EnCours','Valide','Rejete']),
        ];
    }
}
