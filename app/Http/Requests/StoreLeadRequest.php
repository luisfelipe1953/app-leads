<?php

namespace App\Http\Requests;

use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'           => ['required', 'string', 'min:2', 'max:255'],
            'email'            => ['required', 'email', 'max:255'],
            'telefono'         => ['nullable', 'string', 'max:30'],
            'fuente'           => ['required', 'string', 'in:' . implode(',', Lead::FUENTES)],
            'producto_interes' => ['nullable', 'string', 'max:255'],
            'presupuesto'      => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.min'      => 'El nombre debe tener al menos 2 caracteres.',
            'email.required'  => 'El email es obligatorio.',
            'email.email'     => 'El formato del email no es válido.',
            'fuente.required' => 'La fuente es obligatoria.',
            'fuente.in'       => 'La fuente debe ser: instagram, facebook, landing_page, referido u otro.',
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
