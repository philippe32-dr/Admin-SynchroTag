<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignObjectRequest extends FormRequest
{
    /**
     * Déterminer si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Récupérer les règles de validation qui s'appliquent à la requête.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'object_name' => 'required|string|max:255',
            'object_photo' => 'required|image|max:2048', // 2MB max
            'object_range' => 'required|integer|min:1',
        ];
    }
    
    /**
     * Messages d'erreur personnalisés
     */
    public function messages(): array
    {
        return [
            'object_name.required' => 'Le nom de l\'objet est requis.',
            'object_photo.required' => 'Une photo de l\'objet est requise.',
            'object_photo.image' => 'Le fichier doit être une image.',
            'object_photo.max' => 'La photo ne doit pas dépasser 2 Mo.',
            'object_range.required' => 'La portée est requise.',
            'object_range.integer' => 'La portée doit être un nombre entier.',
            'object_range.min' => 'La portée doit être supérieure à 0.',
        ];
    }
}
