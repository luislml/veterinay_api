<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlanRequest extends FormRequest
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
        // Si es UPDATE (PUT/PATCH), los campos son opcionales
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'name' => ($isUpdate ? 'sometimes' : 'required') . '|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|string|max:100',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'El nombre del plan es obligatorio.',
            'name.string'   => 'El nombre del plan debe ser un texto válido.',
            'name.max'      => 'El nombre del plan no puede tener más de 255 caracteres.',

            'description.string' => 'La descripción debe ser un texto válido.',

            'type.string' => 'El tipo del plan debe ser un texto válido.',
            'type.max'    => 'El tipo no puede superar los 100 caracteres.',
        ];
    }
}
