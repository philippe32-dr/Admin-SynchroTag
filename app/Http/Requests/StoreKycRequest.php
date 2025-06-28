<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKycRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $kycId = $this->route('kyc') ? $this->route('kyc')->id : null;
        
        return [
            'user_id' => [
                'required',
                'exists:users,id',
                // S'assurer que l'utilisateur n'a pas déjà un KYC (sauf si c'est une mise à jour)
                Rule::unique('kycs', 'user_id')->ignore($kycId)
            ],
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'nationalite' => 'required|string|max:100',
            'telephone' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9+\s\-()]+$/' // Format de téléphone international simple
            ],
            'adresse_postale' => 'required|string|max:500',
            'numero_npi' => [
                'required',
                'string',
                'size:10', // Exactement 10 caractères
                'regex:/^\d{10}$/', // Uniquement 10 chiffres
                Rule::unique('kycs', 'numero_npi')->ignore($kycId)
            ]
        ];
    }
    
    /**
     * Custom error messages
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'L\'utilisateur est requis.',
            'user_id.exists' => 'L\'utilisateur sélectionné n\'existe pas.',
            'user_id.unique' => 'Cet utilisateur a déjà un KYC.',
            'nom.required' => 'Le nom est requis.',
            'prenom.required' => 'Le prénom est requis.',
            'nationalite.required' => 'La nationalité est requise.',
            'telephone.required' => 'Le numéro de téléphone est requis.',
            'telephone.regex' => 'Le format du numéro de téléphone est invalide.',
            'adresse_postale.required' => 'L\'adresse postale est requise.',
            'numero_npi.required' => 'Le numéro NPI est requis.',
            'numero_npi.size' => 'Le numéro NPI doit contenir exactement 10 chiffres.',
            'numero_npi.regex' => 'Le numéro NPI ne doit contenir que des chiffres.',
            'numero_npi.unique' => 'Ce numéro NPI est déjà utilisé par un autre dossier KYC.',
        ];
    }
}
