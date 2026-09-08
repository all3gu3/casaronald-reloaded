<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validación del formulario de solicitud (el prototipo la tenía comentada).
 * Los nombres de campo son los del formulario de Trabajo Social.
 */
class StoreNinoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fec_sol' => ['required', 'date'],
            'estatus' => ['required', 'string', 'max:50'],
            'hos' => ['required', 'integer', 'exists:hospital,id'],
            'serv' => ['nullable', 'string', 'max:255'],
            'nombre' => ['required', 'string', 'max:255'],
            'app' => ['required', 'string', 'max:255'],
            'apm' => ['required', 'string', 'max:255'],
            'fec_nac' => ['required', 'date', 'before_or_equal:today'],
            'sexo' => ['required', 'in:Masculino,Femenino,Indefinido'],
            'pais' => ['required', 'integer', 'exists:pais,id'],
            'est' => ['required', 'integer', 'exists:estado,id'],
            'mun' => ['required', 'string', 'max:255'],
            'calle' => ['nullable', 'string', 'max:255'],
            'num_c' => ['nullable', 'string', 'max:20'],
            'col' => ['nullable', 'string', 'max:255'],
            'loca' => ['nullable', 'string', 'max:255'],
            'cp' => ['nullable', 'string', 'max:10'],
            'zona' => ['required', 'integer', 'exists:zona,id'],
            'tel1' => ['required', 'string', 'max:20'],
            'tel2' => ['nullable', 'string', 'max:20'],
            'dial' => ['nullable', 'string', 'max:255'],
            'esc' => ['required', 'integer', 'exists:escolaridad,id'],
            'rango' => ['required', 'integer', 'exists:clasificacion_social,id'],
            'sal_min' => ['required', 'integer', 'exists:salario_minimo,id'],
            'trab' => ['required', 'integer', 'exists:trabajador_social,id'],
            'medico' => ['nullable', 'string', 'max:255'],
            'diag' => ['nullable', 'string', 'max:255'],
            'ale_alim' => ['nullable', 'string', 'max:255'],
            'ale_med' => ['nullable', 'string', 'max:255'],
            'tt' => ['nullable', 'array'],
            'tt.*' => ['integer', 'exists:tipo_tratamiento,id'],
            'obs' => ['nullable', 'string', 'max:255'],
            'diet' => ['required', 'integer', 'exists:tipo_dieta,id'],
            'fec_ing' => ['nullable', 'date'],
            'fec_sal' => ['nullable', 'date', 'after_or_equal:fec_ing'],
            'image' => ['nullable', 'image', 'max:4096'],
        ];
    }
}
