<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKycRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',

            'nationalite' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'adresse_postale' => 'required|string|max:255',
            'pdf_cip' => 'required|file|mimes:pdf|max:5120',
        ];
    }
}
