<?php

namespace App\Http\Controllers;

use App\Models\Puce;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\StorePuceRequest;
use App\Http\Requests\UpdatePuceRequest;

class PuceController extends Controller
{
    // Liste des puces avec recherche/filtrage
    public function index(Request $request)
    {
        $query = Puce::with(['client.user']);
        if ($request->filled('search')) {
            $query->where('cle_unique', 'like', "%{$request->search}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $puces = $query->orderBy('id', 'asc')->paginate(10);
        return view('puces.index', compact('puces'));
    }

    // Formulaire création puce
    public function create()
    {
        return view('puces.create');
    }

    // Sauvegarde nouvelle puce
    public function store(StorePuceRequest $request)
    {
        Puce::create($request->validated());
        return redirect()->route('puces.index')->with('success', 'Puce ajoutée');
    }

    // Formulaire édition
    public function edit(Puce $puce)
    {
        return view('puces.edit', compact('puce'));
    }

    // Mise à jour puce
    public function update(UpdatePuceRequest $request, Puce $puce)
    {
        $data = $request->validated();
        
        // Si le statut est changé à "Attribuee"
        if ($data['status'] === 'Attribuee' && $puce->status !== 'Attribuee') {
            $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);
            
            // Vérifier que l'utilisateur a un KYC valide
            $user = User::findOrFail($request->user_id);
            if ($user->statut_kyc !== 'Valide') {
                return back()->withErrors(['user_id' => 'L\'utilisateur sélectionné n\'a pas de KYC valide']);
            }
            
            // Créer un client pour cet utilisateur s'il n'en a pas
            $client = Client::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nom' => $user->nom,
                    'prenom' => $user->prenom,
                    'statusActif' => 'Active'
                ]
            );
            
            // Associer la puce au client
            $puce->client_id = $client->id;
        } elseif ($data['status'] === 'Libre' && $puce->status === 'Attribuee') {
            // Si on passe de "Attribuee" à "Libre", on supprime l'association
            $puce->client_id = null;
        }
        
        $puce->update($data);
        
        return redirect()->route('puces.index')
            ->with('success', 'Puce mise à jour avec succès');
    }

    // Libérer puce (supprimer attribution)
    public function destroy(Puce $puce)
    {
        $puce->update(['status' => 'Libre', 'client_id' => null]);
        return redirect()->route('puces.index')->with('success', 'Puce libérée');
    }

    // Attribuer puce à un client
    public function assign(Request $request, Puce $puce)
    {
        $client = Client::find($request->client_id);
        if ($client && $client->user->statut_kyc === 'Valide') {
            $puce->update(['status' => 'Attribuee', 'client_id' => $client->id]);
            return redirect()->route('puces.index')->with('success', 'Puce attribuée');
        } else {
            return back()->withErrors(['client_id' => 'Client non valide ou KYC non validé']);
        }
    }

    // Libérer puce (bouton dédié)
    public function unassign(Puce $puce)
    {
        $puce->update(['status' => 'Libre', 'client_id' => null]);
        return redirect()->route('puces.index')->with('success', 'Puce libérée');
    }

    // Recherche avancée
    public function search(Request $request)
    {
        return redirect()->route('puces.index', $request->only(['search', 'status']));
    }
}
