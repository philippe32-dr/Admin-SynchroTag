<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use App\Models\Puce;

class AdminController extends Controller
{
    // Affiche le dashboard d'administration
    public function dashboard()
    {
        $usersActifs = User::where('status', 'Active')->count();
        $usersInactifs = User::where('status', 'Inactive')->count();
        $clients = Client::count();
        $pucesLibres = Puce::where('status', 'Libre')->count();
        $pucesAttribuees = Puce::where('status', 'Attribuee')->count();
        return view('admin.dashboard', compact('usersActifs', 'usersInactifs', 'clients', 'pucesLibres', 'pucesAttribuees'));
    }
}
