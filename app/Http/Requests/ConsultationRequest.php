<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConsultationRequest extends FormRequest
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
        return [
            'description' => 'required|string',
            'pet_id'      => 'required|exists:pets,id',
            'reason' => 'required|string',
            //'date'        => 'required|date',
        ];
    }
    public function messages(): array
    {
        return [
            'description.required' => 'La descripción es obligatoria.',
            'reason.required' => 'La razon es obligatoria.',
            'pet_id.required'      => 'El ID de la mascota es obligatorio.',
            'pet_id.exists'        => 'La mascota seleccionada no existe.',
            //'date.required'        => 'La fecha es obligatoria.',
            //'date.date'            => 'La fecha debe tener un formato válido.',
        ];
    }
}
