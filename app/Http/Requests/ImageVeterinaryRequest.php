<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImageVeterinaryRequest extends FormRequest
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
            'veterinary_id' => 'required|exists:veterinaries,id',
            'type' => 'required|in:team,logo,testimonial',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'veterinary_id.required' => 'La veterinaria es obligatoria.',
            'veterinary_id.exists' => 'La veterinaria no existe.',
            'type.required' => 'El tipo es obligatorio.',
            'type.in' => 'El tipo debe ser: team, logo o testimonial.',
            'image.file' => 'El archivo debe ser una imagen válida.',
            'image.mimes' => 'Formatos permitidos: jpg, jpeg, png, webp.',
        ];
    }
}
