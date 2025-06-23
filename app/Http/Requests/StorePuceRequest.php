<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePuceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'cle_unique' => 'required|string|unique:puces,cle_unique',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status' => 'in:Libre,Attribuee',
            'client_id' => 'nullable|exists:clients,id',
        ];
    }
}
