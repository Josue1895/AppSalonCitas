<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|max:60',
            'apellido' => 'required|max:60',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|min:6|confirmed',
            'telefono' => 'nullable|max:10'
        ];
    }
}