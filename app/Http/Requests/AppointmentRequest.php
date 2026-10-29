<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'hora' => ['required', 'date_format:H:i'],
            'servicios' => 'required|array|min:1'
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->has('fecha') || $validator->errors()->has('hora')) {
                return;
            }

            $hora = $this->input('hora');

            if ($hora < '09:00' || $hora > '18:00') {
                $validator->errors()->add('hora', 'Las citas solo se pueden reservar entre las 9:00 a. m. y las 6:00 p. m.');
                return;
            }

            $ahora = now();

            if ($this->input('fecha') === $ahora->toDateString()
                && $hora < $ahora->format('H:i')) {
                $validator->errors()->add('hora', 'La hora debe ser igual o posterior a la hora actual.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'fecha.required' => 'La fecha de la cita es obligatoria.',
            'fecha.date_format' => 'La fecha debe tener un formato válido.',
            'fecha.after_or_equal' => 'La fecha de la cita no puede ser anterior a hoy.',
            'hora.required' => 'La hora de la cita es obligatoria.',
            'hora.date_format' => 'La hora debe tener un formato válido.',
            'servicios.required' => 'Debes seleccionar al menos un servicio.',
            'servicios.min' => 'Debes seleccionar al menos un servicio.',
        ];
    }
}