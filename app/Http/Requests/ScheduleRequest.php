<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
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
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'days' => ($isUpdate ? 'sometimes' : 'required') . '|string|max:255',
            'schedule' => ($isUpdate ? 'sometimes' : 'required') . '|string|max:255',
            'veterinary_id' => ($isUpdate ? 'sometimes' : 'required') . '|exists:veterinaries,id',
        ];
    }
    public function messages(): array
    {
        return [
            // DAYS
            'days.required' => 'Los días son obligatorios.',
            'days.string' => 'Los días deben ser un texto válido.',
            'days.max' => 'Los días no pueden superar los 255 caracteres.',

            // SCHEDULE
            'schedule.required' => 'El horario es obligatorio.',
            'schedule.string' => 'El horario debe ser un texto válido.',
            'schedule.max' => 'El horario no puede superar los 255 caracteres.',

            // VETERINARY_ID
            'veterinary_id.required' => 'Debe seleccionar una veterinaria.',
            'veterinary_id.exists' => 'La veterinaria seleccionada no existe.',
        ];
    }
}
