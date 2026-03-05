<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContentVeterinaryRequest extends FormRequest
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
        $allowedTypes = ['service', 'specialty', 'testimonial'];

        if ($this->user()->hasRole('admin')) {
            array_unshift($allowedTypes, 'banner');
        }

        return [
            'veterinary_id' => 'required|exists:veterinaries,id',
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'type' => 'required|in:' . implode(',', $allowedTypes),
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'veterinary_id.required' => 'La veterinaria es obligatoria.',
            'veterinary_id.exists' => 'La veterinaria no existe.',
            'title.required' => 'El título es obligatorio.',
            'type.required' => 'El tipo es obligatorio.',
            'type.in' => 'El tipo seleccionado no es válido.',
        ];
    }
}
