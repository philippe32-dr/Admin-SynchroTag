<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Kyc;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    /**
     * Les attributs pouvant être assignés en masse.
     * - id : identifiant unique
     * - nom : nom de l'utilisateur
     * - prenom : prénom de l'utilisateur
     * - email : email unique
     * - status : Active ou Inactive
     * - statut_kyc : NonSoumis, EnCours, Valide, Rejete
     */
    /**
     * Statuts KYC possibles pour un utilisateur
     */
    public const KYC_NON_SOUMIS = 'NonSoumis';
    public const KYC_EN_COURS = 'EnCours';
    public const KYC_VALIDE = 'Valide';
    public const KYC_REJETE = 'Rejete';

    protected $fillable = [
        'id', 'nom', 'prenom', 'email', 'status', 'statut_kyc', 'profile_photo',
        'email_verification_code', 'reset_password_code', 'password', 'remember_token', 'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    /**
     * Get the client associated with the user.
     */
    /**
     * Get the KYC associated with the user.
     */
    public function kyc()
    {
        return $this->hasOne(Kyc::class);
    }

    /**
     * Get the client associated with the user.
     */
    public function client()
    {
        return $this->hasOne(Client::class);
    }
    

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

