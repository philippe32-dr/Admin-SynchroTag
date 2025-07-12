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
        'nom',
        'prenom',
        'email',
        'password',
        'status',
        'statut_kyc',
        'telephone',
        'adresse',
        'date_naissance',
        'lieu_naissance',
        'nationalite',
        'type_piece_identite',
        'numero_piece_identite',
        'date_emission_piece',
        'date_expiration_piece',
        'adresse_postale',
        'code_postal',
        'ville',
        'pays',
        'photo_profil',
        'piece_identite_recto',
        'piece_identite_verso',
        'justificatif_domicile',
        'email_verified_at',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['photo_profil_url'];

    /**
     * Get the URL for the user's profile photo.
     *
     * @return string
     */
    public function getPhotoProfilUrlAttribute()
    {
        if (!$this->photo_profil) {
            return null;
        }
        
        if (str_starts_with($this->photo_profil, 'http')) {
            return $this->photo_profil;
        }
        
        return asset('storage/' . ltrim($this->photo_profil, '/'));
    }
    
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

