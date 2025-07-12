<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\VerifyEmailRequest;
use App\Http\Requests\Api\Auth\ForgotPasswordRequest;
use App\Http\Requests\Api\Auth\VerifyCodeRequest;
use App\Http\Requests\Api\Auth\ResetPasswordRequest;
use App\Http\Requests\Api\Auth\UpdateProfileRequest;
use App\Mail\VerificationCodeEmail;
use App\Mail\ResetPasswordCodeEmail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    // Inscription
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $verificationCode = (string) random_int(100000, 999999);
        $data['email_verification_code'] = $verificationCode;

        // Gestion de la photo de profil
        if ($request->hasFile('photo_profil')) {
            $path = $request->file('photo_profil')->store('profiles', 'public');
            $data['photo_profil'] = $path;
        }

        $user = User::create($data);

        try {
            // Forcer l'utilisation du mailer 'log' pour les tests
            Mail::mailer('log')
                ->to($user->email)
                ->send(new VerificationCodeEmail($verificationCode));

            // Pour le débogage, affiche le code dans la réponse
            return response()->json([
                'message' => 'Code de vérification généré avec succès',
                'verification_code' => $verificationCode,
                'note' => 'En production, ce code serait envoyé par email. Pour le moment, il est affiché ici pour les tests.'
            ], 201);
        } catch (\Exception $e) {
            // En cas d'erreur, retourner le code quand même pour les tests
            return response()->json([
                'message' => 'Erreur lors de l\'envoi de l\'email, mais le code a été généré',
                'verification_code' => $verificationCode,
                'error' => $e->getMessage()
            ], 201);
        }
    }

    // Vérification du code de validation d'email
    public function verifyEmail(VerifyEmailRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();
        
        // Pour le débogage
        if (!$user) {
            return response()->json([
                'message' => 'Aucun utilisateur trouvé avec cet email',
                'email' => $request->email,
                'code_reçu' => $request->code
            ], 422);
        }
        
        if ($user->email_verification_code !== $request->code) {
            return response()->json([
                'message' => 'Code de vérification invalide',
                'email' => $user->email,
                'code_attendu' => $user->email_verification_code,
                'code_reçu' => $request->code,
                'type_code_attendu' => gettype($user->email_verification_code),
                'type_code_reçu' => gettype($request->code)
            ], 422);
        }
        
        $user->update([
            'email_verified_at' => now(),
            'email_verification_code' => null,
        ]);
        
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Email vérifié avec succès',
            'user'  => $user->only('id', 'nom', 'prenom', 'email', 'statut_kyc'),
            'token' => $token,
        ]);
    }

    // Connexion
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }
        if (!$user->email_verified_at) {
            return response()->json(['message' => 'Email non vérifié'], 403);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'user'  => $user->only('id', 'nom', 'prenom', 'email', 'statut_kyc'),
            'token' => $token,
        ]);
    }

    // Déconnexion
    public function logout(): JsonResponse
    {
        request()->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Déconnexion réussie']);
    }

    // Demande de réinitialisation de mot de passe
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();
        
        // Si l'utilisateur n'existe pas, on retourne une réponse générique
        // pour ne pas révéler que l'email n'existe pas
        if (!$user) {
            return response()->json([
                'message' => 'Si votre email existe dans notre système, vous recevrez un code de réinitialisation.'
            ]);
        }
        
        $code = (string) random_int(100000, 999999);
        $user->update(['reset_password_code' => $code]);

        try {
            // Forcer l'utilisation du mailer 'log' pour les tests
            Mail::mailer('log')
                ->to($user->email)
                ->send(new ResetPasswordCodeEmail($code));

            return response()->json([
                'message' => 'Si votre email existe dans notre système, vous recevrez un code de réinitialisation.',
                'reset_code' => $code, // A supprimer en production
                'note' => 'En production, seul le message générique serait affiché.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la demande de réinitialisation.'
            ], 500);
        }
    }

    // Vérification du code de réinitialisation
    public function verifyCode(VerifyCodeRequest $request): JsonResponse
    {
        // Debug: Afficher la requête SQL brute
        \Log::info('Vérification du code de réinitialisation', [
            'email' => $request->email,
            'code_reçu' => $request->code
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Aucun utilisateur trouvé avec cet email',
                'email' => $request->email
            ], 422);
        }

        // Debug: Afficher les informations de l'utilisateur et du code
        $isCodeValid = $user->reset_password_code == $request->code;
        
        \Log::info('Détails de vérification du code', [
            'user_id' => $user->id,
            'email' => $user->email,
            'code_attendu' => $user->reset_password_code,
            'code_reçu' => $request->code,
            'type_code_attendu' => gettype($user->reset_password_code),
            'type_code_reçu' => gettype($request->code),
            'est_egal' => $isCodeValid ? 'oui' : 'non'
        ]);

        if (!$isCodeValid) {
            return response()->json([
                'message' => 'Code de réinitialisation invalide',
                'email' => $user->email,
                'code_attendu' => $user->reset_password_code,
                'code_reçu' => $request->code,
                'type_code_attendu' => gettype($user->reset_password_code),
                'type_code_reçu' => gettype($request->code),
                'est_egal' => $isCodeValid ? 'oui' : 'non'
            ], 422);
        }

        return response()->json([
            'message' => 'Code valide',
            'email' => $user->email,
            'code' => $request->code
        ]);
    }

    // Réinitialisation du mot de passe
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        // Debug: Log de la tentative de réinitialisation
        \Log::info('Tentative de réinitialisation de mot de passe', [
            'email' => $request->email
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json([
                'message' => 'Aucun utilisateur trouvé avec cet email',
                'email' => $request->email
            ], 422);
        }

        // Vérification plus détaillée du code
        $isCodeValid = $user->reset_password_code == $request->code;
        
        if (!$isCodeValid) {
            return response()->json([
                'message' => 'Code de réinitialisation invalide',
                'email' => $user->email,
                'code_attendu' => $user->reset_password_code,
                'code_reçu' => $request->code,
                'type_code_attendu' => gettype($user->reset_password_code),
                'type_code_reçu' => gettype($request->code),
                'est_egal' => $isCodeValid ? 'oui' : 'non'
            ], 422);
        }
        $user->update([
            'password' => Hash::make($request->password),
            'reset_password_code' => null,
        ]);
        return response()->json(['message' => 'Mot de passe réinitialisé']);
    }

    // Profil utilisateur
    public function user(): JsonResponse
    {
        $user = auth()->user();
        return response()->json([
            'id' => $user->id,
            'nom' => $user->nom,
            'prenom' => $user->prenom,
            'email' => $user->email,
            'telephone' => $user->telephone,
            'photo_profil_url' => $user->photo_profil_url,
            'statut_kyc' => $user->statut_kyc,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at
        ]);
    }

    /**
     * Mettre à jour le profil utilisateur
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = auth()->user();
        $data = $request->validated();

        // Gestion de la photo de profil
        if ($request->hasFile('photo_profil')) {
            // Supprimer l'ancienne photo si elle existe
            if ($user->photo_profil) {
                Storage::delete('public/' . $user->photo_profil);
            }
            
            // Stocker la nouvelle photo
            $path = $request->file('photo_profil')->store('profiles', 'public');
            $data['photo_profil'] = $path;
        }

        $user->update($data);

        return response()->json([
            'message' => 'Profil mis à jour avec succès',
            'user' => [
                'id' => $user->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'telephone' => $user->telephone,
                'photo_profil_url' => $user->photo_profil_url,
                'statut_kyc' => $user->statut_kyc,
            ]
        ]);
    }
}
