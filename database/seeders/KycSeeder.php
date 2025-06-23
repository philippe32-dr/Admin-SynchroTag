<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kyc;
use App\Models\User;

class KycSeeder extends Seeder
{
    public function run()
    {
        foreach (User::where('statut_kyc', '!=', 'Valide')->get() as $user) {
            Kyc::factory()->create([
                'user_id' => $user->id,
                'nom' => $user->nom ?? 'Nom',
                'prenom' => $user->prenom ?? 'Prenom',
                'status' => 'EnCours',
            ]);
            // Met à jour le statut KYC de l'utilisateur pour permettre l'attribution de puce
            $user->statut_kyc = 'EnCours';
            $user->save();
        }
    }
}
