<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleRequest extends FormRequest
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
            // Ya no pedimos date_sale ni user_id
            'veterinary_id' => ($isUpdate ? 'sometimes' : 'required') . '|exists:veterinaries,id',
            'state' => ($isUpdate ? 'sometimes' : 'required') . '|string|in:Completado,Cancelado',
            'amount' => ($isUpdate ? 'sometimes' : 'required') . '|numeric|min:0',
            'client_id' => ($isUpdate ? 'sometimes' : 'required') . '|exists:clients,id',
            'discount' => 'nullable|numeric|min:0',
            'products' => ($isUpdate ? 'sometimes' : 'required') . '|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price_unit' => 'required|numeric|min:0',
        ];
    }
    public function messages(): array
    {
        return [
            'veterinary_id.required' => 'La veterinaria es obligatoria.',
            'veterinary_id.exists' => 'La veterinaria seleccionada no existe.',

            'state.required' => 'El estado es obligatorio.',
            'state.in' => 'El estado debe ser: Completado o Cancelado.',

            'amount.required' => 'El monto es obligatorio.',
            'amount.numeric' => 'El monto debe ser un número.',

            'client_id.required' => 'Debe seleccionar un cliente.',
            'client_id.exists' => 'El cliente no existe.',

            'discount.numeric' => 'El descuento debe ser un número.',

            'products.required' => 'Debe enviar al menos un producto.',
            'products.array' => 'Los productos deben estar en un arreglo.',

            'products.*.product_id.required' => 'El ID del producto es obligatorio.',
            'products.*.product_id.exists' => 'El producto no existe.',

            'products.*.quantity.required' => 'La cantidad es obligatoria.',
            'products.*.quantity.integer' => 'La cantidad debe ser un número entero.',
            'products.*.quantity.min' => 'La cantidad debe ser al menos 1.',

            'products.*.price_unit.required' => 'El precio unitario es obligatorio.',
            'products.*.price_unit.numeric' => 'El precio unitario debe ser numérico.',
            'products.*.price_unit.min' => 'El precio unitario no puede ser negativo.',
        ];
    }
}
