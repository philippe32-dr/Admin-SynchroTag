<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Kyc extends Model
{
    use HasFactory;

    /**
     * Statuts possibles d'un KYC
     */
    public const STATUS_EN_COURS = 'EnCours';
    public const STATUS_VALIDE = 'Valide';
    public const STATUS_REJETE = 'Rejete';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'client_id',
        'nom',
        'prenom',
        'nationalite',
        'telephone',
        'adresse_postale',
        'numero_npi',
        'status',
        'raison_rejet',
        'photo_recto',
        'photo_verso',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'validated_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];
    
    /**
     * Les attributs qui doivent être mutés en dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'validated_at',
        'rejected_at',
    ];

    /**
     * Règles de validation pour la création d'un KYC
     *
     * @param int|null $kycId ID du KYC pour ignorer les contraintes d'unicité
     * @return array
     */
    public static function rules(?int $kycId = null): array
    {
        return [
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('kycs', 'user_id')->ignore($kycId)
            ],
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'nationalite' => ['required', 'string', 'max:100'],
            'telephone' => ['required', 'string', 'max:20'],
            'adresse_postale' => ['required', 'string', 'max:500'],
            'numero_npi' => [
                'required',
                'string',
                'size:10',
                'regex:/^\d{10}$/',
                Rule::unique('kycs', 'numero_npi')->ignore($kycId)
            ],
            'status' => ['sometimes', 'required', 'in:' . implode(',', [
                self::STATUS_EN_COURS,
                self::STATUS_VALIDE,
                self::STATUS_REJETE
            ])],
            'raison_rejet' => ['nullable', 'string', 'max:1000', 'required_if:status,' . self::STATUS_REJETE],
        ];
    }

    /**
     * Messages de validation personnalisés
     *
     * @return array
     */
    public static function messages(): array
    {
        return [
            'user_id.unique' => 'Cet utilisateur a déjà un KYC.',
            'numero_npi.unique' => 'Ce numéro NPI est déjà utilisé par un autre dossier KYC.',
            'numero_npi.regex' => 'Le numéro NPI doit contenir exactement 10 chiffres.',
            'raison_rejet.required_if' => 'La raison du rejet est obligatoire lorsque le statut est "Rejeté".',
        ];
    }

    /**
     * Valide les données du KYC
     *
     * @param array $data
     * @param int|null $kycId
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validate(array $data, ?int $kycId = null)
    {
        return Validator::make($data, self::rules($kycId), self::messages());
    }

    /**
     * Relation avec le modèle User
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le modèle Client
     *
     * @return BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
    
    /**
     * Relation avec l'utilisateur qui a validé le KYC
     *
     * @return BelongsTo
     */
    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
    
    /**
     * Relation avec l'utilisateur qui a rejeté le KYC
     *
     * @return BelongsTo
     */
    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Vérifie si le KYC est en cours
     *
     * @return bool
     */
    public function isEnCours(): bool
    {
        return $this->status === self::STATUS_EN_COURS;
    }

    /**
     * Vérifie si le KYC est validé
     *
     * @return bool
     */
    public function isValide(): bool
    {
        return $this->status === self::STATUS_VALIDE;
    }

    /**
     * Vérifie si le KYC est rejeté
     *
     * @return bool
     */
    public function isRejete(): bool
    {
        return $this->status === self::STATUS_REJETE;
    }
}
