<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrarEscaneoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'codigo' => strtoupper(trim((string) $this->input('codigo'))),
        ]);
    }

    public function rules(): array
    {
        return [
            'codigo' => ['required', 'string', 'size:6', 'exists:nino,qr'],
            'accion' => ['required', 'in:lavanderia,comedor,escuela,transporte,entrada,salida'],
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.exists' => 'Ningún expediente corresponde a ese código.',
            'codigo.size' => 'El código debe tener 6 caracteres.',
        ];
    }
}
