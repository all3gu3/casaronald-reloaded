<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAcompananteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nino' => ['required', 'integer', 'exists:nino,id'],
            'nombre' => ['required', 'string', 'max:255'],
            'app' => ['required', 'string', 'max:255'],
            'apm' => ['required', 'string', 'max:255'],
            'fec_nac' => ['required', 'date', 'before:today'],
            'sexo' => ['required', 'in:Masculino,Femenino,Indefinido'],
            'paren' => ['required', 'integer', 'exists:parentesco,id'],
            'edoSalud' => ['required', 'integer', 'exists:edo_salud,id'],
            'esc' => ['required', 'integer', 'exists:escolaridad,id'],
            'ocu' => ['required', 'integer', 'exists:ocupacion,id'],
            'trab' => ['required', 'boolean'],
            'goce' => ['nullable', 'boolean'],
            'seg' => ['required', 'boolean'],
            'casa' => ['required', 'boolean'],
            'asist' => ['required', 'boolean'],
            'rent' => ['nullable', 'integer', 'min:0'],
            'dep_ec' => ['required', 'integer', 'min:0'],
            'ing' => ['required', 'integer', 'min:0'],
            'obs' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:4096'],
        ];
    }
}
