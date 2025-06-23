<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\User;

class ClientSeeder extends Seeder
{
    public function run()
    {
        // Ne crée aucun client automatiquement. Les clients seront ajoutés manuellement via le dashboard après validation KYC.
    }
}
