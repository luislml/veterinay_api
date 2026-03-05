<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShoppingRequest extends FormRequest
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
            'veterinary_id' => ($isUpdate ? 'sometimes' : 'required') . '|exists:veterinaries,id',
            'state' => ($isUpdate ? 'sometimes' : 'required') . '|string|in:Completado,Cancelado',
            'products' => ($isUpdate ? 'sometimes' : 'required') . '|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price_unit' => 'required|numeric|min:0',
        ];
    }
    public function messages(): array
    {
        return [
            // VETERINARY_ID
            'veterinary_id.required' => 'La veterinaria es obligatoria.',
            'veterinary_id.exists' => 'La veterinaria seleccionada no existe.',

            // STATE
            'state.required' => 'El estado de la compra es obligatorio.',
            'state.string' => 'El estado debe ser un texto válido.',
            'state.in' => 'El estado debe ser: Completado o Cancelado.',

            // PRODUCTS
            'products.required' => 'Debe enviar al menos un producto.',
            'products.array' => 'Los productos deben enviarse en un arreglo.',
            'products.min' => 'Debe enviar al menos un producto.',

            'products.*.product_id.required' => 'Cada producto debe tener un ID.',
            'products.*.product_id.exists' => 'El producto seleccionado no existe.',

            'products.*.quantity.required' => 'Debe indicar la cantidad de cada producto.',
            'products.*.quantity.integer' => 'La cantidad debe ser un número entero.',
            'products.*.quantity.min' => 'La cantidad debe ser al menos 1.',

            'products.*.price_unit.required' => 'Debe indicar el precio unitario de cada producto.',
            'products.*.price_unit.numeric' => 'El precio unitario debe ser un número.',
            'products.*.price_unit.min' => 'El precio unitario no puede ser negativo.',
        ];
    }
}
