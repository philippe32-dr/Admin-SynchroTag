<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKycRequest;
use App\Models\Kyc;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class KycController extends Controller
{
    /**
     * Messages de réponse courants
     */
    private const MESSAGES = [
        'kyc_exists' => 'Une demande KYC existe déjà pour cet utilisateur.',
        'kyc_submitted' => 'Demande KYC soumise avec succès',
        'kyc_error' => 'Une erreur est survenue lors de la soumission du KYC',
        'kyc_not_found' => 'Aucun KYC trouvé pour cet utilisateur',
        'kyc_retrieved' => 'Informations KYC récupérées avec succès',
    ];
    
    /**
     * Codes de statut HTTP courants
     */
    private const HTTP_STATUS = [
        'success' => 200,
        'created' => 201,
        'validation_error' => 422,
        'server_error' => 500,
    ];
    /**
     * Soumettre une demande KYC
     *
     * @param StoreKycRequest $request
     * @return JsonResponse
     *
     * @OA\Post(
     *     path="/api/kyc",
     *     summary="Soumettre une demande KYC",
     *     description="Permet à un utilisateur de soumettre une demande de KYC",
     *     operationId="submitKyc",
     *     tags={"KYC"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreKycRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="KYC soumis avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Demande KYC soumise avec succès"),
     *             @OA\Property(property="status", type="string", example="EnCours"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="EnCours"),
     *                 @OA\Property(property="created_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation ou logique métier échouée",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Une demande KYC existe déjà pour cet utilisateur."),
     *             @OA\Property(property="status", type="string", example="EnCours"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="EnCours"),
     *                 @OA\Property(property="created_at", type="string", format="date-time")
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreKycRequest $request): JsonResponse
    {
        $user = $request->user();
        
        // Vérifier si l'utilisateur a déjà un KYC
        if ($user->kyc) {
            return $this->jsonResponse(
                self::MESSAGES['kyc_exists'],
                self::HTTP_STATUS['validation_error'],
                [
                    'status' => $user->kyc->status,
                    'data' => $user->kyc->only(['id', 'status', 'created_at'])
                ]
            );
        }
        
        // Vérifier que l'utilisateur a le bon statut pour soumettre un KYC
        if (!in_array($user->statut_kyc, [User::KYC_NON_SOUMIS, User::KYC_REJETE])) {
            return $this->jsonResponse(
                'Votre statut actuel ne permet pas de soumettre un KYC.',
                self::HTTP_STATUS['validation_error']
            );
        }
        
        try {
            DB::beginTransaction();
            
            // Créer le KYC avec les données validées
            $kyc = new Kyc($request->validated());
            $kyc->user_id = $user->id;
            $kyc->status = Kyc::STATUS_EN_COURS;
            $kyc->save();
            
            // Mettre à jour le statut KYC de l'utilisateur
            $user->statut_kyc = User::KYC_EN_COURS;
            $user->save();
            
            DB::commit();
            
            // Journaliser la création du KYC
            Log::info('Nouveau KYC soumis', [
                'kyc_id' => $kyc->id,
                'user_id' => $user->id,
                'numero_npi' => $kyc->numero_npi
            ]);
            
            return $this->jsonResponse(
                self::MESSAGES['kyc_submitted'],
                self::HTTP_STATUS['created'],
                [
                    'status' => $kyc->status,
                    'data' => $kyc->only(['id', 'status', 'created_at'])
                ]
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Journaliser l'erreur avec plus de contexte
            Log::error('Erreur lors de la soumission du KYC', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->jsonResponse(
                self::MESSAGES['kyc_error'],
                self::HTTP_STATUS['server_error'],
                [
                    'error' => config('app.debug') ? $e->getMessage() : null
                ]
            );
        }
    }
    
    /**
     * Obtenir le statut du KYC de l'utilisateur connecté
     * 
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/kyc/status",
     *     summary="Obtenir le statut du KYC",
     *     description="Récupère le statut actuel du KYC de l'utilisateur connecté",
     *     operationId="getKycStatus",
     *     tags={"KYC"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Statut KYC récupéré avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="has_kyc", type="boolean", example=true),
     *             @OA\Property(property="status", type="string", example="EnCours"),
     *             @OA\Property(property="message", type="string", example="Statut KYC récupéré avec succès"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="EnCours"),
     *                 @OA\Property(property="numero_npi", type="string", example="1234567890"),
     *                 @OA\Property(property="nom", type="string", example="Dupont"),
     *                 @OA\Property(property="prenom", type="string", example="Jean"),
     *                 @OA\Property(property="nationalite", type="string", example="Française"),
     *                 @OA\Property(property="telephone", type="string", example="+33123456789"),
     *                 @OA\Property(property="adresse_postale", type="string", example="123 rue de Paris, 75001 Paris"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     )
     * )
     */
    public function status(): JsonResponse
    {
        $user = Auth::user();
        
        // Charger la relation KYC pour éviter les requêtes N+1
        $user->load('kyc');
        
        if (!$user->kyc) {
            return $this->jsonResponse(
                self::MESSAGES['kyc_not_found'],
                self::HTTP_STATUS['success'],
                [
                    'has_kyc' => false,
                    'status' => User::KYC_NON_SOUMIS
                ]
            );
        }
        
        $kyc = $user->kyc;
        
        return $this->jsonResponse(
            self::MESSAGES['kyc_retrieved'],
            self::HTTP_STATUS['success'],
            [
                'has_kyc' => true,
                'status' => $kyc->status,
                'data' => [
                    'id' => $kyc->id,
                    'status' => $kyc->status,
                    'numero_npi' => $kyc->numero_npi,
                    'nom' => $kyc->nom,
                    'prenom' => $kyc->prenom,
                    'nationalite' => $kyc->nationalite,
                    'telephone' => $kyc->telephone,
                    'adresse_postale' => $kyc->adresse_postale,
                    'created_at' => $kyc->created_at->toDateTimeString(),
                    'updated_at' => $kyc->updated_at->toDateTimeString()
                ]
            ]
        );
    }
    
    /**
     * Méthode utilitaire pour formater les réponses JSON de manière cohérente
     *
     * @param string $message
     * @param int $statusCode
     * @param array $data
     * @return JsonResponse
     */
    private function jsonResponse(string $message, int $statusCode = 200, array $data = []): JsonResponse
    {
        $response = [
            'message' => $message,
            'status' => $statusCode >= 200 && $statusCode < 300 ? 'success' : 'error',
            'timestamp' => now()->toDateTimeString(),
        ];
        
        if (!empty($data)) {
            $response = array_merge($response, $data);
        }
        
        return response()->json($response, $statusCode);
    }
}
