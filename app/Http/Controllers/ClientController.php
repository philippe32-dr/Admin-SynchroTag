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
        $client->update($data);
        return redirect()->route('clients.index')->with('success', 'Client mis à jour');
    }
    
    /**
     * Libère une puce d'un client
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Client  $client
     * @param  \App\Models\Puce  $puce
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removePuce(Request $request, Client $client, Puce $puce)
    {
        try {
            // Vérifier que le client a bien cette puce
            if ($puce->client_id !== $client->id) {
                return back()->with('error', 'Erreur: Cette puce n\'est pas attribuée à ce client.');
            }
            
            // Vérifier qu'il reste au moins une puce
            $puceCount = $client->puces()->count();
            if ($puceCount <= 1) {
                return back()->with('error', 'Action impossible: Un client doit avoir au moins une puce attribuée.');
            }
            
            // Libérer la puce
            $puce->update([
                'client_id' => null,
                'status' => 'Libre'
            ]);
            
            // Mettre à jour le statut de la puce
            $puce->status = 'Libre';
            $puce->save();
            
            // Rafraîchir la relation pour refléter les changements
            $client->load('puces');
            
            return back()->with([
                'success' => 'Succès: La puce a été libérée avec succès.',
                'puce_name' => $puce->object_name ?? 'Puce ' . $puce->id
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la libération de la puce: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la libération de la puce.');
        }
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
