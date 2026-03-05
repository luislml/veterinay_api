<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromotionRequest extends FormRequest
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
            'description' => 'nullable|string',
            'date_init' => ($isUpdate ? 'sometimes' : 'required') . '|date',
            'date_end' => ($isUpdate ? 'sometimes' : 'required') . '|date|after_or_equal:date_init',
            'veterinary_id' => ($isUpdate ? 'sometimes' : 'required') . '|exists:veterinaries,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ];
    }
    public function messages(): array
    {
        return [
            // NAME
            'name.required' => 'El nombre de la promoción es obligatorio.',
            'name.string' => 'El nombre debe ser un texto válido.',
            'name.max' => 'El nombre no puede superar los 255 caracteres.',

            // DESCRIPTION
            'description.string' => 'La descripción debe ser un texto válido.',

            // DATE INIT
            'date_init.required' => 'La fecha de inicio es obligatoria.',
            'date_init.date' => 'La fecha de inicio debe tener un formato válido.',

            // DATE END
            'date_end.required' => 'La fecha de fin es obligatoria.',
            'date_end.date' => 'La fecha de fin debe ser una fecha válida.',
            'date_end.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',

            // VETERINARY_ID
            'veterinary_id.required' => 'Debe seleccionar una veterinaria.',
            'veterinary_id.exists' => 'La veterinaria seleccionada no existe.',
        ];
    }
}
