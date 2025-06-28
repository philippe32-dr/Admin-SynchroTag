<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Désactiver les événements pour accélérer les seeders
        User::flushEventListeners();
        \App\Models\Client::flushEventListeners();
        \App\Models\Puce::flushEventListeners();
        \App\Models\Kyc::flushEventListeners();
        
        // Création de 100 utilisateurs avec KYC en cours
        \App\Models\User::factory(100)
            ->has(\App\Models\Kyc::factory()->state([
                'status' => 'EnCours',
                'client_id' => null,
                'numero_npi' => fn() => (string) mt_rand(1000000000, 9999999999),
            ]))
            ->create([
                'statut_kyc' => 'EnCours',
            ]);

        // Création de 100 utilisateurs sans KYC
        \App\Models\User::factory(100)
            ->create([
                'statut_kyc' => 'NonSoumis',
            ]);

        // Création de 50 puces libres
        \App\Models\Puce::factory(50)
            ->create([
                'status' => 'Libre',
                'client_id' => null,
            ]);

        // Aucun client ni puce attribuée (conforme à la logique KYC initiale)

    }
}
