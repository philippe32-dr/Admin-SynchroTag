<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
    protected $fillable = [
        'id', 'nom', 'prenom', 'email', 'status', 'statut_kyc',
        'password', 'remember_token', 'email_verified_at',
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
    public function client()
    {
        return $this->hasOne(Client::class);
    }
    public function kycs()
{
    return $this->hasMany(\App\Models\Kyc::class, 'user_id');
}
}

