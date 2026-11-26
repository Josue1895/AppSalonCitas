<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'fecha' => 'required|date',
            'hora' => 'required'
        ];

        // Solo exigir servicios al crear cita
        if ($this->isMethod('post') && !$this->route('id')) {

            $rules['servicios'] = 'required|array|min:1';
        }

        return $rules;
    }
}


