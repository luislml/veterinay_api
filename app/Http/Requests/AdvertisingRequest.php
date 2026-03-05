<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdvertisingRequest extends FormRequest
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
        // Captura el ID del registro actual en caso de update
        $advertisingId = $this->route('advertising')->id ?? null;

        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'date_init' => 'sometimes|required|date',
            'date_end' => 'sometimes|required|date|after_or_equal:date_init',
            'veterinary_id' => 'sometimes|required|exists:veterinaries,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser un texto.',
            'name.max' => 'El nombre no puede superar los 255 caracteres.',

            'description.string' => 'La descripción debe ser un texto.',

            'date_init.required' => 'La fecha de inicio es obligatoria.',
            'date_init.date' => 'La fecha de inicio no es válida.',

            'date_end.required' => 'La fecha final es obligatoria.',
            'date_end.date' => 'La fecha final no es válida.',
            'date_end.after_or_equal' => 'La fecha final debe ser mayor o igual a la fecha inicial.',

            'veterinary_id.required' => 'Debe seleccionar una veterinaria.',
            'veterinary_id.exists' => 'La veterinaria seleccionada no existe.',
        ];
    }
}
