<?php

namespace App\Http\Requests;

use App\Models\Horario;
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
            'servicios' => ['required', 'array', 'min:1'],
            'servicios.*' => ['required', 'integer', 'exists:servicios,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (
                $validator->errors()->has('fecha') ||
                $validator->errors()->has('hora')
            ) {
                return;
            }

            $fecha = $this->input('fecha');
            $hora = $this->input('hora');

            // Convierte el día de la semana al formato usado en la tabla horarios:
            // Lunes = 1, ..., Domingo = 7.
            $diaSemana = (int) date('N', strtotime($fecha));

            $horario = Horario::where('dia_semana', $diaSemana)->first();

            if (!$horario || !$horario->activo) {
                $validator->errors()->add(
                    'fecha',
                    'No hay atención disponible para el día seleccionado.'
                );
                return;
            }

            if (
                $hora < substr($horario->hora_inicio, 0, 5) ||
                $hora >= substr($horario->hora_fin, 0, 5)
            ) {
                $validator->errors()->add(
                    'hora',
                    'La hora seleccionada está fuera del horario de atención.'
                );
                return;
            }

            $ahora = now();

            if (
                $fecha === $ahora->toDateString() &&
                $hora < $ahora->format('H:i')
            ) {
                $validator->errors()->add(
                    'hora',
                    'La hora debe ser igual o posterior a la hora actual.'
                );
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
            'servicios.*.exists' => 'Uno de los servicios seleccionados no existe.',
        ];
    }
}
