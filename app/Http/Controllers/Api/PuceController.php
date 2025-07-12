<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignObjectRequest;
use App\Http\Requests\UpdateObjectRequest;
use App\Models\Puce;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class PuceController extends Controller
{
    
    public function index(): JsonResponse
    {
        $user = auth()->user();
        
        $puces = Puce::where('client_id', $user->client?->id)
            ->get(['id', 'cle_unique as numero', 'status', 'object_name', 'object_photo', 'object_range', 'client_id']);
            
        return response()->json(['puces' => $puces->map(function($puce) {
            return array_merge($puce->toArray(), [
                'object_photo_url' => $puce->object_photo_url
            ]);
        })]);
    }

    /**
     * Associer un objet à une puce
     */
    public function assignObject(AssignObjectRequest $request, Puce $puce): JsonResponse
    {
        $user = auth()->user();
        
        // Vérifier que la puce appartient bien au client de l'utilisateur
        if ($puce->client_id !== $user->client?->id) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }
        
        // Vérifier que la puce est bien attribuée
        if ($puce->status !== 'Attribuee') {
            return response()->json(['message' => 'La puce doit être attribuée pour ajouter un objet.'], 422);
        }
        
        $data = $request->validated();
        
        // Gestion du téléchargement de la photo
        if ($request->hasFile('object_photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($puce->object_photo) {
                Storage::delete('public/' . $puce->object_photo);
            }
            
            // Stocker la nouvelle photo
            $path = $request->file('object_photo')->store('objects', 'public');
            $data['object_photo'] = $path;
        }
        
        $puce->update($data);
        
        return response()->json(['puce' => $puce->only('id', 'numero_serie', 'status', 'object_name', 'object_photo', 'object_range')]);
    }
    
    /**
     * Mettre à jour un objet associé à une puce
     */
    public function updateObject(UpdateObjectRequest $request, Puce $puce): JsonResponse
    {
        $user = auth()->user();
        
        // Vérifier que la puce appartient bien au client de l'utilisateur
        if ($puce->client_id !== $user->client?->id) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }
        
        $data = $request->validated();
        
        // Gestion du téléchargement de la photo
        if ($request->hasFile('object_photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($puce->object_photo) {
                Storage::delete('public/' . $puce->object_photo);
            }
            
            // Stocker la nouvelle photo
            $path = $request->file('object_photo')->store('objects', 'public');
            $data['object_photo'] = $path;
        }
        
        $puce->update($data);
        
        return response()->json(['puce' => $puce->only('id', 'numero_serie', 'status', 'object_name', 'object_photo', 'object_range')]);
    }
}
