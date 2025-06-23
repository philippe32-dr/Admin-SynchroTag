<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Puce;

class PuceSeeder extends Seeder
{
    public function run()
    {
        $clients = \App\Models\Client::all();
        $totalClients = $clients->count();
        // Crée d'abord une puce attribuée à chaque client
        foreach ($clients as $client) {
            \App\Models\Puce::factory()->create([
                'status' => 'attribue',
                'client_id' => $client->id,
            ]);
        }
        // Crée le reste des puces, libres
        $remaining = max(0, 30 - $totalClients);
        \App\Models\Puce::factory()->count($remaining)->create([
            'status' => 'libre',
            'client_id' => null,
        ]);
    }
}
