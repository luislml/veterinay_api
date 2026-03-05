<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class PetRequest extends FormRequest
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
        //$isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'name' => 'sometimes|required|string|max:255',
            'race_id' => 'sometimes|required|exists:races,id',
            'client_id' => 'sometimes|required|exists:clients,id',
            'color' => 'nullable|string|max:50',
            'gender' => 'nullable|string|max:10',
            'birthday' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser un texto.',
            'name.max' => 'El nombre no puede superar los 255 caracteres.',

            'race_id.required' => 'Debe seleccionar una raza.',
            'race_id.exists' => 'La raza seleccionada no existe.',

            'client_id.required' => 'Debe seleccionar un cliente.',
            'client_id.exists' => 'El cliente seleccionado no existe.',

            'color.required' => 'Debe seleccionar un color.',
            'color.string' => 'El color debe ser un texto.',
            'color.max' => 'El color no puede superar 50 caracteres.',

            'gender.required' => 'Debe seleccionar un género.',
            'gender.string' => 'El género debe ser un texto.',
            'gender.max' => 'El género no puede superar 10 caracteres.',

            'birthdate.required' => 'La edad es obligatoria.',
            'birthdate.date' => 'La edad debe ser una fecha.',
        ];
    }
    // Forzar validación JSON
    protected function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'errors' => $validator->errors(),
        ], 422);

        throw new ValidationException($validator, $response);
    }
}
