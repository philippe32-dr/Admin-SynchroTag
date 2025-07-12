<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historique extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'longitude_moi',
        'latitude_moi',
        'longitude_cible',
        'latitude_cible',
        'distance',
        'date',
        'heure'
    ];

    protected $casts = [
        'date' => 'date',
        'heure' => 'datetime:H:i:s',
        'distance' => 'float',
        'longitude_moi' => 'float',
        'latitude_moi' => 'float',
        'longitude_cible' => 'float',
        'latitude_cible' => 'float'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
