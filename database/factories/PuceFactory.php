<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Puce;

class PuceFactory extends Factory
{
    protected $model = Puce::class;

    public function definition(): array
    {
        return [
            'cle_unique' => strtoupper(fake()->unique()->bothify('PUCE-####-????')),
            'latitude' => fake()->randomFloat(6, -90, 90),
            'longitude' => fake()->randomFloat(6, -180, 180),
            'status' => fake()->randomElement(['libre','attribue']),
        ];
    }
}
