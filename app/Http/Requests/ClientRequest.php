<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
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

        // Regla unique para CI
        $ciRule = $isUpdate
            ? Rule::unique('clients', 'ci')->ignore($this->route('client')->id ?? null)
            : Rule::unique('clients', 'ci');

        return [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',

            'ci' => array_merge(
                [$isUpdate ? 'sometimes' :  'string', 'max:20'],
                [$ciRule]
            ),

            'veterinary_id' => 'required|array|min:1',
            'veterinary_id.*' => 'exists:veterinaries,id',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser un texto.',
            'name.max' => 'El nombre no puede superar los 255 caracteres.',

            'last_name.required' => 'El apellido es obligatorio.',
            'last_name.string' => 'El apellido debe ser un texto.',
            'last_name.max' => 'El apellido no puede superar los 255 caracteres.',

            //'ci.required' => 'El C.I. es obligatorio.',
            'ci.string' => 'El C.I. debe ser un texto.',
            'ci.max' => 'El C.I. no puede superar 20 caracteres.',
            'ci.unique' => 'El C.I. ya está registrado.',
            
            'phone.string' => 'El teléfono debe ser un texto.',
            'phone.max' => 'El teléfono no puede superar 20 caracteres.',

            'address.string' => 'La dirección debe ser un texto.',
            'address.max' => 'La dirección no puede superar los 255 caracteres.',
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
