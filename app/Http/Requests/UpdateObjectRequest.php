<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateObjectRequest extends FormRequest
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
            'object_name' => 'sometimes|string|max:255',
            'object_photo' => 'sometimes|image|max:2048', // 2MB max
            'object_range' => 'sometimes|integer|min:1',
        ];
    }
    
    /**
     * Messages d'erreur personnalisés
     */
    public function messages(): array
    {
        return [
            'object_name.string' => 'Le nom de l\'objet doit être une chaîne de caractères.',
            'object_name.max' => 'Le nom de l\'objet ne doit pas dépasser 255 caractères.',
            'object_photo.image' => 'Le fichier doit être une image.',
            'object_photo.max' => 'La photo ne doit pas dépasser 2 Mo.',
            'object_range.integer' => 'La portée doit être un nombre entier.',
            'object_range.min' => 'La portée doit être supérieure à 0.',
        ];
    }
}
