<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Historique;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'adresse',
        'telephone',
        'email',
        'user_id',
        'statusActif'
    ];

    public function historiques()
    {
        return $this->hasMany(Historique::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function kycs()
    {
        return $this->hasMany(Kyc::class);
    }
    public function puces()
    {
        return $this->hasMany(Puce::class);
    }
}
