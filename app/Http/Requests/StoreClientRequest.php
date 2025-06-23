<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id|unique:clients,user_id',
            'puces' => 'required|array|min:1',
            'puces.*' => 'exists:puces,id',
        ];
    }
}
