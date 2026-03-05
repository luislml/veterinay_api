<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfigurationRequest extends FormRequest
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
            'veterinary_id'      => 'sometimes|required|exists:veterinaries,id',

            'color_primary'      => 'nullable|string|max:50',
            'color_secondary'    => 'nullable|string|max:50',

            'about_us'           => 'nullable|string',
            'description_team'   => 'nullable|string',

            'phone'              => 'nullable|string|max:20',
            'phone_emergency'    => 'nullable|string|max:20',

            'favicon' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
        ];
    }
    public function messages(): array
    {
        return [
            'veterinary_id.required' => 'Debe seleccionar una veterinaria.',
            'veterinary_id.exists'   => 'La veterinaria seleccionada no existe.',

            'color_primary.string'   => 'El color primario debe ser un texto.',
            'color_primary.max'      => 'El color primario no puede superar los 50 caracteres.',

            'color_secondary.string' => 'El color secundario debe ser un texto.',
            'color_secondary.max'    => 'El color secundario no puede superar 50 caracteres.',

            'about_us.string'        => 'La sección "Sobre nosotros" debe ser un texto.',

            'description_team.string'=> 'La descripción del equipo debe ser un texto.',

            'phone.string'           => 'El teléfono debe ser un texto.',
            'phone.max'              => 'El teléfono no debe superar 20 caracteres.',

            'phone_emergency.string' => 'El teléfono de emergencia debe ser un texto.',
            'phone_emergency.max'    => 'El teléfono de emergencia no debe superar 20 caracteres.',
        ];
    }
}
