<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RaceRequest extends FormRequest
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
            'type_pet_id' => ($isUpdate ? 'sometimes' : 'required') . '|exists:type_pets,id',
        ];
    }
    public function messages(): array
    {
        return [
            // NAME
            'name.required' => 'El nombre de la raza es obligatorio.',
            'name.string' => 'El nombre debe ser un texto válido.',
            'name.max' => 'El nombre no puede superar los 255 caracteres.',

            // TYPE_PET_ID
            'type_pet_id.required' => 'Debe seleccionar un tipo de mascota.',
            'type_pet_id.exists' => 'El tipo de mascota seleccionado no existe.',
        ];
    }
}
