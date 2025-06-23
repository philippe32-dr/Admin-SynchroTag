<?php

namespace App\Http\Controllers;

use App\Models\Kyc;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreKycRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KycController extends Controller
{
    // Affiche la liste des KYC avec onglets
    public function index(Request $request)
    {
        $status = $request->get('status', 'EnCours');
        $kycs = Kyc::with('client.user')
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(15);
        return view('kyc.index', compact('kycs', 'status'));
    }

    // Formulaire de création
    public function create()
    {
        // On ne propose que les utilisateurs qui n'ont pas déjà un KYC en cours ou validé
        $users = \App\Models\User::whereDoesntHave('kycs', function($q){
            $q->whereIn('status', ['EnCours','Valide']);
        })->get();
        return view('kyc.create', compact('users'));
    }

    // Enregistre un nouveau KYC
    public function store(StoreKycRequest $request)
    {
        $data = $request->validated();
        $user = \App\Models\User::findOrFail($data['user_id']);
        // Récupère nom et prénom depuis l'utilisateur (plus depuis le formulaire)
        $data['nom'] = $user->nom;
        $data['prenom'] = $user->prenom;
        if ($request->hasFile('pdf_cip')) {
            $data['pdf_cip'] = $request->file('pdf_cip')->store('kyc', 'public');
        }
        // Si l'utilisateur courant est admin, le KYC est validé directement
        $data['status'] = 'Valide';
        $kyc = Kyc::create($data);
        $user->statut_kyc = 'Valide';
        $user->save();
        return redirect()->route('kyc.index')->with('success', 'KYC validé directement (admin).');
    }

    // Affiche les détails d'un KYC
    public function show(Kyc $kyc)
    {
        $kyc->load(['user', 'client']);
        return view('kyc.show', compact('kyc'));
    }

    // Valider un KYC
    public function validateKyc(Request $request, Kyc $kyc)
    {
        $kyc->status = 'Valide';
        $kyc->save();
        // Met à jour le statut KYC de l'utilisateur
        $user = $kyc->user;
        $user->statut_kyc = 'Valide';
        $user->save();
        return redirect()->route('kyc.index', ['status'=>'EnCours'])->with('success', 'KYC validé.');
    }

    // Rejeter un KYC
    public function reject(Request $request, Kyc $kyc)
    {
        $kyc->status = 'Rejete';
        $kyc->save();
        if ($kyc->client && $kyc->client->user) {
            $kyc->client->user->statut_kyc = 'Rejete';
            $kyc->client->user->save();
        }
        return redirect()->route('kyc.index', ['status'=>'EnCours'])->with('success', 'KYC rejeté.');
    }

    // Recherche KYC par nom/prenom + filtre status
    public function search(Request $request)
    {
        $query = Kyc::with('client.user');
        if ($request->filled('nom')) {
            $query->where('nom', 'like', '%'.$request->nom.'%');
        }
        if ($request->filled('prenom')) {
            $query->where('prenom', 'like', '%'.$request->prenom.'%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $kycs = $query->orderByDesc('created_at')->paginate(15);
        $status = $request->status ?? 'EnCours';
        return view('kyc.index', compact('kycs', 'status'));
    }
}
