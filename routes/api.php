<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PuceController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\KycController;
// Route racine pour afficher la documentation de l'API
Route::get('/', function () {
    return response()->json([
        'message' => 'Bienvenue sur l\'API Admin-SynchroTag',
        'endpoints' => [
            'POST /register' => 'Enregistrer un nouvel utilisateur',
            'POST /verify-email' => 'Vérifier l\'email avec un code',
            'POST /login' => 'Se connecter',
            'POST /password/forgot' => 'Demander un code de réinitialisation',
            'POST /password/verify-code' => 'Vérifier le code de réinitialisation',
            'POST /password/reset' => 'Réinitialiser le mot de passe',
            'POST /logout' => 'Se déconnecter (protégé)',
            'GET /user' => 'Récupérer le profil utilisateur (protégé)',
        ]
    ]);
});

// Routes d'authentification
Route::match(['get', 'post'], '/register', [AuthController::class, 'register']);
Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/password/forgot', [AuthController::class, 'forgotPassword']);
Route::post('/password/verify-code', [AuthController::class, 'verifyCode']);
Route::post('/password/reset', [AuthController::class, 'resetPassword']);

// Routes protégées par authentification
Route::middleware('auth:sanctum')->group(function () {
    // Authentification
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Routes pour les puces
    Route::get('/puces', [PuceController::class, 'index']);
    Route::post('/puces/{puce}/assign-object', [PuceController::class, 'assignObject']);
    Route::put('/puces/{puce}/update-object', [PuceController::class, 'updateObject']);
    
    // Routes pour le profil utilisateur
    Route::put('/profile', [ProfileController::class, 'update']);
    
    // Routes pour les KYC
    Route::post('/kyc', [KycController::class, 'store']);
    Route::get('/kyc/status', [KycController::class, 'status']);
});
