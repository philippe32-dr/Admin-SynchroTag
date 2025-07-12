<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Historique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HistoriqueController extends Controller
{
    /**
     * Affiche l'historique d'un client
     */
    public function index($clientId)
    {
        $historiques = Historique::where('client_id', $clientId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($historiques);
    }

    /**
     * Enregistre un nouvel historique
     */
    public function store(Request $request, $clientId)
    {
        $validator = Validator::make($request->all(), [
            'longitude_moi' => 'required|numeric',
            'latitude_moi' => 'required|numeric',
            'longitude_cible' => 'required|numeric',
            'latitude_cible' => 'required|numeric',
            'distance' => 'required|numeric|min:0',
            'date' => 'required|date',
            'heure' => 'required|date_format:H:i:s'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $historique = Historique::create([
            'client_id' => $clientId,
            'longitude_moi' => $request->longitude_moi,
            'latitude_moi' => $request->latitude_moi,
            'longitude_cible' => $request->longitude_cible,
            'latitude_cible' => $request->latitude_cible,
            'distance' => $request->distance,
            'date' => $request->date,
            'heure' => $request->heure
        ]);

        return response()->json($historique, 201);
    }

    /**
     * Affiche un historique spécifique
     */
    public function show($clientId, $id)
    {
        $historique = Historique::where('client_id', $clientId)
            ->findOrFail($id);

        return response()->json($historique);
    }

    /**
     * Supprime un historique
     */
    public function destroy($clientId, $id)
    {
        $historique = Historique::where('client_id', $clientId)
            ->findOrFail($id);
            
        $historique->delete();

        return response()->json(null, 204);
    }
}
