<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Puce extends Model
{
    use HasFactory;

    // Statuts possibles d'une puce
    public const STATUS_LIBRE = 'Libre';
    public const STATUS_ATTRIBUEE = 'Attribuee';

    protected $fillable = [
        'numero_puce', 'cle_unique', 'latitude', 'longitude', 'status', 'client_id',
        'object_name', 'object_photo', 'object_range'
    ];

    /**
     * Règles de validation pour une puce
     *
     * @return array
     */
    public static function rules()
    {
        return [
            'numero_puce' => 'required|string|max:100|unique:puces,numero_puce',
            'status' => [
                'required',
                'string',
                Rule::in([self::STATUS_LIBRE, self::STATUS_ATTRIBUEE])
            ],
            'client_id' => 'nullable|exists:clients,id',
            'object_name' => 'nullable|string|max:255',
            'object_photo' => 'nullable|string|max:255',
            'object_range' => 'nullable|integer|min:0',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'cle_unique' => 'nullable|string|max:255',
        ];
    }
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'object_photos' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    
    /**
     * Get the URL of the object photo
     */
    public function getObjectPhotoUrlAttribute()
    {
        return $this->object_photo ? asset('storage/' . $this->object_photo) : null;
    }
}
