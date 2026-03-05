<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VaccineRequest extends FormRequest
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
            'name' => ($isUpdate ? 'sometimes' : 'required') . '|string|max:255',
            'pet_id' => ($isUpdate ? 'sometimes' : 'required') . '|exists:pets,id',
            //'date' => ($isUpdate ? 'sometimes' : 'required') . '|date',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la vacuna es obligatorio.',
            'name.string' => 'El nombre debe ser un texto válido.',
            'name.max' => 'El nombre no puede superar los 255 caracteres.',

            'pet_id.required' => 'Debe seleccionar una mascota.',
            'pet_id.exists' => 'La mascota seleccionada no existe.',

            //'date.required' => 'La fecha de aplicación es obligatoria.',
            //'date.date' => 'La fecha debe tener un formato válido.',
        ];
    }
}
