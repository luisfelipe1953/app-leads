<?php

namespace App\Http\Requests;

use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'           => ['sometimes', 'string', 'min:2', 'max:255'],
            'email'            => ['sometimes', 'email', 'max:255'],
            'telefono'         => ['nullable', 'string', 'max:30'],
            'fuente'           => ['sometimes', 'string', 'in:' . implode(',', Lead::FUENTES)],
            'producto_interes' => ['nullable', 'string', 'max:255'],
            'presupuesto'      => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.min'  => 'El nombre debe tener al menos 2 caracteres.',
            'email.email' => 'El formato del email no es válido.',
            'fuente.in'   => 'La fuente debe ser: instagram, facebook, landing_page, referido u otro.',
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Error de validación.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
