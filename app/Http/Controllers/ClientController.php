<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Puce;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
                      $qu->where('email', 'like', "%$search%");
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
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'puces' => 'required|array|exists:puces,id'
        ]);
        $user = User::findOrFail($data['user_id']);
        if ($user->statut_kyc !== 'Valide') {
            return back()->withErrors(['user_id' => 'Utilisateur non éligible (KYC non validé)']);
        }
        $client = Client::create([
            'user_id' => $user->id,
            'nom' => $user->nom,
            'prenom' => $user->prenom,
            'statusActif' => 'Active',
        ]);
        foreach ($data['puces'] as $puceId) {
            $puce = Puce::find($puceId);
            if ($puce) {
                $puce->update(['status' => 'Attribuee', 'client_id' => $client->id]);
            }
        }
        return redirect()->route('clients.index')->with('success', 'Client créé avec succès');
    }

    // Formulaire édition
    public function edit(Client $client)
    {
        $availablePuces = Puce::where('status', 'Libre')->orderBy('id')->get();
        $client->load('user'); // Charger la relation user
        return view('clients.edit', compact('client', 'availablePuces'));
    }
    
    public function update(Request $request, Client $client)
    {
        $action = $request->input('action', 'update_client');
        
        if ($action === 'update_client') {
            // Règles de validation minimales
            $rules = [
                'nom' => 'nullable|string|max:255',
                'prenom' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255|unique:users,email,' . ($client->user_id ?? 'NULL') . ',id',
                'statusActif' => 'nullable|in:Active,Inactive',
                'password' => 'nullable|confirmed|min:8',
            ];
            
            // Validation des données
            $validatedData = $request->validate($rules);
            
            // Mise à jour du client
            $client->fill($request->only(['nom', 'prenom', 'statusActif']));
            $client->save();
            
            // Mise à jour de l'utilisateur associé
            if ($client->user) {
                $user = $client->user;
                $userData = $request->only(['nom', 'prenom', 'email']);
                
                // Ne mettre à jour le mot de passe que s'il est fourni
                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }
                
                $user->fill($userData);
                $user->save();
            }
            
            return redirect()->route('clients.index')->with('success', 'Client mis à jour avec succès');
        }

        if ($action === 'assign_puces') {
            $validatedData = $request->validate([
                'new_puces' => 'required|array|exists:puces,id'
            ]);
            
            $puces = Puce::whereIn('id', $validatedData['new_puces'])->where('status', 'Libre')->get();
            if ($puces->isEmpty()) {
                return redirect()->route('clients.edit', $client)->with('error', 'Aucune puce valide sélectionnée pour l\'attribution.');
            }
            
            foreach ($puces as $puce) {
                $puce->update(['client_id' => $client->id, 'status' => 'Attribuee']);
            }
            
            return redirect()->route('clients.edit', $client)->with('success', count($puces) . ' puce(s) attribuée(s) avec succès.');
        }

        if ($action === 'deassign_puces') {
            $validatedData = $request->validate([
                'deassign_puces' => 'required|array|exists:puces,id'
            ]);
            
            if ($client->puces->count() <= count($validatedData['deassign_puces'])) {
                return redirect()->route('clients.edit', $client)->with('error', 'Un client doit conserver au moins une puce.');
            }
            
            $puces = Puce::whereIn('id', $validatedData['deassign_puces'])->where('client_id', $client->id)->get();
            if ($puces->isEmpty()) {
                return redirect()->route('clients.edit', $client)->with('error', 'Aucune puce valide sélectionnée pour la désattribution.');
            }
            
            foreach ($puces as $puce) {
                $puce->update(['client_id' => null, 'status' => 'Libre']);
            }
            
            return redirect()->route('clients.edit', $client)->with('success', count($puces) . ' puce(s) désattribuée(s) avec succès.');
        }

        return redirect()->route('clients.edit', $client)->with('error', 'Aucune action valide spécifiée.');
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