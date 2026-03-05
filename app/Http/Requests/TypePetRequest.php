<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TypePetRequest extends FormRequest
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
        $typePetId = $this->route('type_pet')->id ?? null;
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'name' => ($isUpdate ? 'sometimes' : 'required') 
                . '|string|max:255|unique:type_pets,name,' . $typePetId,
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del tipo de mascota es obligatorio.',
            'name.string' => 'El nombre debe ser un texto válido.',
            'name.max' => 'El nombre no puede superar los 255 caracteres.',
            'name.unique' => 'Ya existe un tipo de mascota con este nombre.',
        ];
    }
}
