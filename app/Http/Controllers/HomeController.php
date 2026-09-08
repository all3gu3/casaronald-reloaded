<?php

namespace App\Http\Controllers;

use App\Enums\Servicio;
use App\Models\EdoSalud;
use App\Models\EntradaSalida;
use App\Models\Escolaridad;
use App\Models\Estado;
use App\Models\Hospital;
use App\Models\Nino;
use App\Models\Ocupacion;
use App\Models\Pais;
use App\Models\Parentesco;
use App\Models\RegistroServicio;
use App\Models\SalarioMinimo;
use App\Models\TipoDieta;
use App\Models\TipoTratamiento;
use App\Models\TrabajadorSocial;
use App\Models\Zona;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function inicio(): View
    {
        return view('welcome');
    }

    public function expedientes(): View
    {
        return view('expedientes', [
            'hospitales' => Hospital::orderBy('hospital')->get(),
            'paises' => Pais::all(),
            'estados' => Estado::orderBy('estado')->get(),
            'zonas' => Zona::all(),
            'escolaridades' => Escolaridad::all(),
            'salario' => SalarioMinimo::all(),
            'trabajadores' => TrabajadorSocial::orderBy('trabajador_social')->get(),
            'dietas' => TipoDieta::all(),
            'parentescos' => Parentesco::orderBy('parentesco')->get(),
            'ocupaciones' => Ocupacion::orderBy('ocupacion')->get(),
            'edosSalud' => EdoSalud::all(),
            'tratamientos' => TipoTratamiento::all(),
            'ninos' => Nino::select('id', 'nombre', 'apellido_paterno', 'apellido_materno', 'qr')->get(),
        ]);
    }

    public function registros(): View
    {
        return view('registros', [
            'ultimasEntradas' => EntradaSalida::with('nino')->latest('entrada')->limit(3)->get(),
            'serviciosPrevia' => collect(Servicio::cases())->map(fn (Servicio $servicio) => [
                'servicio' => $servicio,
                'registros' => RegistroServicio::where('servicio', $servicio)
                    ->with('nino')
                    ->latest()
                    ->limit(3)
                    ->get(),
            ]),
        ]);
    }

    public function lavanderia(): View
    {
        return view('datatables.lavanderia');
    }

    public function comedor(): View
    {
        return view('datatables.comedor');
    }

    public function escuela(): View
    {
        return view('datatables.escuela');
    }

    public function transporte(): View
    {
        return view('datatables.transporte');
    }

    public function entradasSalidas(): View
    {
        return view('datatables.entradas_salidas');
    }
}
