<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
            'address' => 'required|string',
            'veterinary_id' => 'required|exists:veterinaries,id',
            'address_type' => 'required|string|in:physical,social_media,map',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ];
    }
    public function messages()
    {
        return [
            'address.required' => 'La dirección es obligatoria.',
            'address.string' => 'La dirección debe ser un texto.',
            'address.max' => 'La dirección no puede superar 255 caracteres.',
            'veterinary_id.required' => 'El ID de la veterinaria es obligatorio.',
            'veterinary_id.exists' => 'La veterinaria seleccionada no existe.',
            'address_type.required' => 'The address type is required.',
            'address_type.string' => 'The address type must be a valid string.',
            'address_type.in' => 'The address type must be either: physical, social_media, map.',
        ];
    }
}
