<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::factory()->count(10)->create([
            'password' => bcrypt('password'),
        ]);
        // Ajoute un admin test
        User::factory()->create([
            'nom' => 'Admin',
            'prenom' => 'Test',
            'email' => 'admin@example.com',
            'statut_kyc' => 'NonSoumis',
            'password' => bcrypt('password'),
        ]);
    }
}
