<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $product = $this->route('product');
        $productId = $product->id ?? null;

        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'name' => ($isUpdate ? 'sometimes' : 'required') . '|string|max:255',
            'price' => ($isUpdate ? 'sometimes' : 'required') . '|numeric|min:0',
            'stock' => ($isUpdate ? 'sometimes' : 'required') . '|integer|min:0',
            'code' => 'nullable|string|max:50',

            'veterinary_ids' => ($isUpdate ? 'sometimes' : 'required') . '|array|min:1',
            'veterinary_ids.*' => 'integer',

            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del producto es obligatorio.',
            'price.required' => 'El precio es obligatorio.',
            'stock.required' => 'El stock es obligatorio.',
            'veterinary_ids.required' => 'Debes asignar al menos una veterinaria.',
            'veterinary_ids.array' => 'El formato de veterinarias no es válido.',
            'veterinary_ids.min' => 'Debes seleccionar al menos una veterinaria.'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'errors' => $validator->errors(),
        ], 422);

        throw new ValidationException($validator, $response);
    }
}
