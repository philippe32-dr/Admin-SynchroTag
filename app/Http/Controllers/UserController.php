<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    // Affiche la liste des utilisateurs avec recherche/filtrage
    public function index(Request $request)
    {
        $query = User::query();
        // Recherche texte
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function($sub) use ($q) {
                $sub->where('nom', 'like', "%$q%")
                    ->orWhere('prenom', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%") ;
            });
        }
        // Filtrage status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        // Filtrage statut_kyc
        if ($request->filled('statut_kyc')) {
            $query->where('statut_kyc', $request->input('statut_kyc'));
        }
        $users = $query->orderBy('id', 'desc')->paginate(10);
        return view('users.index', compact('users'));
    }

    // Formulaire création
    public function create()
    {
        return view('users.create');
    }

    // Sauvegarde nouvel utilisateur
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['status'] = $data['status'] ?? 'Active';
        $data['statut_kyc'] = 'NonSoumis';
        $data['password'] = bcrypt($data['password']);
        User::create($data);
        return redirect()->route('users.index')->with('success', 'Utilisateur créé');
    }

    // Formulaire édition
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    // Mise à jour utilisateur
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        $user->update($data);
        return redirect()->route('users.index')->with('success', 'Utilisateur modifié');
    }

    // Désactive un utilisateur
    public function destroy(User $user)
    {
        $user->update(['status' => 'Inactive']);
        return redirect()->route('users.index')->with('success', 'Utilisateur désactivé');
    }

    // Recherche/filtrage POST (redirige vers index avec paramètres)
    public function search(Request $request)
    {
        $params = array_filter($request->only(['q','status','statut_kyc']));
        return redirect()->route('users.index', $params);
    }
}
