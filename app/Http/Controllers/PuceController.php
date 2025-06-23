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
        $query = Puce::with('client');
        if ($request->filled('search')) {
            $query->where('cle_unique', 'like', "%{$request->search}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $puces = $query->paginate(10);
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
        $puce->update($request->validated());
        return redirect()->route('puces.index')->with('success', 'Puce mise à jour');
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
