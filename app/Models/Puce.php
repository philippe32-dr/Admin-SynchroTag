<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Puce extends Model
{
    use HasFactory;

    protected $fillable = [
        'cle_unique', 'latitude', 'longitude', 'status', 'client_id'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
