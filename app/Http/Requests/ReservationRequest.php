<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservationRequest extends FormRequest
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
        'name_reservation' => ($isUpdate ? 'sometimes' : 'required') . '|string|max:255',
        'last_name_reservation' => ($isUpdate ? 'sometimes' : 'required') . '|string|max:255',
        'ci_reservation' => ($isUpdate ? 'sometimes' : 'required') . '|string|max:20',
        'phone_reservation' => ($isUpdate ? 'sometimes' : 'required') . '|string|max:20',
        'details' => 'nullable|string',
        'date' => ($isUpdate ? 'sometimes' : 'required') . '|date',
        ];
    }
    public function messages(): array
    {
        return [
        'veterinary_id.required' => 'Debe seleccionar una veterinaria.',
        'veterinary_id.exists' => 'La veterinaria seleccionada no existe.',

        'name_reservation.required' => 'El nombre de la reserva es obligatorio.',
        'last_name_reservation.required' => 'El apellido de la reserva es obligatorio.',
        'ci_reservation.required' => 'La cédula de la persona que reserva es obligatoria.',
        'phone_reservation.required' => 'El teléfono de la persona que reserva es obligatorio.',

        'date.required' => 'La fecha de la reserva es obligatoria.',
        'date.date' => 'La fecha debe tener un formato válido.',
    ];
    }
}
