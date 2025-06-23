<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use App\Models\Puce;
use Illuminate\Http\Request;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;

class ClientController extends Controller
{
    // Liste des clients avec recherche/filtrage
    public function index(Request $request)
    {
        $query = Client::with(['user', 'puces']);
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%$search%")
                  ->orWhere('prenom', 'like', "%$search%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('email', 'like', "%$search%") ;
                  });
            });
        }
        if ($request->filled('statusActif')) {
            $query->where('statusActif', $request->input('statusActif'));
        }
        $clients = $query->paginate(10);
        return view('clients.index', compact('clients'));
    }

    // Formulaire création client
    public function create()
    {
        $users = User::where('statut_kyc', 'Valide')->doesntHave('client')->get();
        $pucesLibres = Puce::where('status', 'Libre')->get();
        return view('clients.create', compact('users', 'pucesLibres'));
    }

    // Sauvegarde nouveau client
    public function store(StoreClientRequest $request)
    {
        $data = $request->validated();
        $user = User::findOrFail($data['user_id']);
        if ($user->statut_kyc !== 'Valide') {
            return back()->withErrors(['user_id' => 'Utilisateur non éligible (KYC non validé)']);
        }
        if (empty($data['puces'])) {
            return back()->withErrors(['puces' => 'Veuillez sélectionner au moins une puce.']);
        }
        $client = Client::create([
            'user_id' => $user->id,
            'nom' => $user->nom,
            'prenom' => $user->prenom,
            'statusActif' => 'Active',
        ]);
        // Attribuer les puces
        // On force l'attribution des puces sélectionnées, même si leur statut n'est pas 'Libre' (pour éviter tout bug de synchro)
        foreach ($data['puces'] as $puceId) {
            $puce = Puce::find($puceId);
            if ($puce) {
                $puce->status = 'Attribuee';
                $puce->client_id = $client->id;
                $puce->save();
            }
        }
        return redirect()->route('clients.index')->with('success', 'Client créé avec succès');
    }

    // Formulaire édition
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    // Mise à jour client
    public function update(UpdateClientRequest $request, Client $client)
    {
        $data = $request->validated();
        $client->update(['statusActif' => $data['statusActif']]);
        return redirect()->route('clients.index')->with('success', 'Client mis à jour');
    }

    // Désactivation client
    public function deactivate(Client $client)
    {
        $client->update(['statusActif' => 'Inactive']);
        return redirect()->route('clients.index')->with('success', 'Client désactivé');
    }

    // Détail client
    public function show(Client $client)
    {
        $client->load(['user', 'puces']);
        return view('clients.show', compact('client'));
    }

    // Recherche avancée
    public function search(Request $request)
    {
        return redirect()->route('clients.index', $request->only(['search', 'statusActif']));
    }
}
