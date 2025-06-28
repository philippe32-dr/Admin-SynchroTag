<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Mettre à jour le profil de l'utilisateur connecté
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = auth()->user();
        $data = $request->validated();
        
        // Gestion du téléchargement de la photo de profil
        if ($request->hasFile('profile_photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($user->profile_photo) {
                $oldPath = str_replace('/storage', 'public', $user->profile_photo);
                Storage::delete($oldPath);
            }
            
            $path = $request->file('profile_photo')->store('public/profiles');
            $data['profile_photo'] = Storage::url($path);
        }
        
        $user->update($data);
        
        return response()->json([
            'user' => $user->only('id', 'nom', 'prenom', 'email', 'profile_photo', 'statut_kyc')
        ]);
    }
}
