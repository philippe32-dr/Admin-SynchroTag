<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Search for users with valid KYC
     */
    public function search(Request $request)
    {
        \Log::info('Search request received', ['query' => $request->input('q')]);
        try {
            $search = $request->input('q', '');
            
            $query = User::where('statut_kyc', 'Valide')
                ->when($search, function($query) use ($search) {
                    $query->where(function($q) use ($search) {
                        $q->where('nom', 'like', "%{$search}%")
                          ->orWhere('prenom', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
                })
                ->with(['client' => function($query) {
                    $query->select('id', 'user_id');
                }])
                ->orderBy('prenom')
                ->orderBy('nom')
                ->limit(50);
                
            $users = $query->get(['id', 'prenom', 'nom', 'email'])
                ->map(function($user) {
                    $user->is_client = !is_null($user->client);
                    unset($user->client);
                    return $user;
                });
                
            return response()->json($users);
            
        } catch (\Exception $e) {
            $errorMessage = 'Erreur lors de la recherche d\'utilisateurs: ' . $e->getMessage();
            \Log::error($errorMessage, [
                'exception' => $e,
                'query' => $request->input('q')
            ]);
            return response()->json([
                'error' => 'Une erreur est survenue lors de la recherche d\'utilisateurs',
                'details' => config('app.debug') ? $errorMessage : null
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
