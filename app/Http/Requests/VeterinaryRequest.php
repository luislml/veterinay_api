<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class VeterinaryRequest extends FormRequest
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
        $veterinaryId = $this->route('veterinary')->id ?? null;

        return [
            'name' => 'required|string|max:255|unique:veterinaries,name,' . $veterinaryId,
            'plan_id' => 'required|exists:plans,id',
            'user_id' => 'required|exists:users,id',
        ];
    }
    public function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'errors' => $validator->errors(),
        ], 422);

        throw new ValidationException($validator, $response);
    }
    public function messages()
    {
        return [
            'name.required' => 'El nombre de la veterinaria es obligatorio.',
            'name.string' => 'El nombre debe ser un texto.',
            'name.max' => 'El nombre no puede superar 255 caracteres.',
            'name.unique' => 'Este nombre ya está en uso.',
            'plan_id.required' => 'Debe seleccionar un plan.',
            'plan_id.exists' => 'El plan seleccionado no existe.',
            'user_id.required' => 'Debe seleccionar un usuario.',
            'user_id.exists' => 'El usuario seleccionado no existe.', // <-- Mensaje claro si no existe
        ];
    }
}
