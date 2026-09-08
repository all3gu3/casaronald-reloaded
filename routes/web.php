<?php

use App\Http\Controllers\AcompananteController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EscanearController;
use App\Http\Controllers\ExpedienteController;
use App\Http\Controllers\FichaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NinoController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Documentación del sistema (pública: no contiene datos de familias)
Route::prefix('documentacion-expediente')->group(function () {
    Route::view('/', 'docs.index')->name('documentacion');
    Route::view('/proyecto', 'docs.proyecto')->name('documentacion-proyecto');
    Route::view('/tecnico', 'docs.tecnico')->name('documentacion-tecnico');
    Route::view('/guia', 'docs.guia')->name('documentacion-guia');
    Route::redirect('/arquitectura', '/documentacion-expediente/tecnico');
});

// Las URL viejas siguen funcionando: se compartieron antes del renombre
Route::redirect('/casita-secreta', '/documentacion-expediente');
Route::redirect('/casita-secreta/proyecto', '/documentacion-expediente/proyecto');
Route::redirect('/casita-secreta/tecnico', '/documentacion-expediente/tecnico');
Route::redirect('/casita-secreta/arquitectura', '/documentacion-expediente/tecnico');
Route::redirect('/casita-secreta/guia', '/documentacion-expediente/guia');

// Autenticación
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:10,1');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Páginas
    Route::get('/', [HomeController::class, 'inicio'])->name('inicio');
    Route::redirect('/familias', '/expedientes'); // la URL vieja sigue funcionando
    Route::get('/expedientes', [HomeController::class, 'expedientes'])->name('expedientes');
    Route::get('/registros', [HomeController::class, 'registros'])->name('registros');
    Route::get('/registros-lavanderia', [HomeController::class, 'lavanderia'])->name('registros-lavanderia');
    Route::get('/registros-comedor', [HomeController::class, 'comedor'])->name('registros-comedor');
    Route::get('/registros-escuela', [HomeController::class, 'escuela'])->name('registros-escuela');
    Route::get('/registros-transporte', [HomeController::class, 'transporte'])->name('registros-transporte');
    Route::get('/registros-entradas-salidas', [HomeController::class, 'entradasSalidas'])->name('registros-entradas-salidas');

    // Perfil de cuenta: cada quien ve el suyo; los administradores, el de cualquiera
    Route::get('/perfil/{usuario?}', [UserController::class, 'perfil'])->name('perfil');

    // Expediente (las escrituras son POST: el prototipo mutaba estado por GET sin CSRF)
    Route::post('/ninos', [NinoController::class, 'store'])->name('ninos.store');
    Route::get('/ninos/por-qr/{qr}', [NinoController::class, 'porQr'])->name('ninos.por-qr');
    Route::get('/ninos/{nino}', [NinoController::class, 'show'])->name('ninos.show');
    Route::get('/ninos/{nino}/perfil', [NinoController::class, 'perfil'])->name('ninos.perfil');
    Route::get('/ninos/{nino}/editar', [NinoController::class, 'edit'])->name('ninos.edit');
    Route::put('/ninos/{nino}', [NinoController::class, 'update'])->name('ninos.update');
    Route::post('/acompanantes', [AcompananteController::class, 'store'])->name('acompanantes.store');

    // Carnet con QR: se genera al pedirlo y se sirve solo autenticado
    Route::get('/fichas/{nino}', [FichaController::class, 'show'])->name('fichas.show');

    // Reporte PDF del expediente — solo administradores y trabajo social
    Route::get('/expedientes/{nino}/pdf', [ExpedienteController::class, 'pdf'])
        ->middleware('can:descargar-expediente')
        ->name('expedientes.pdf');

    // Registro por escaneo (ventana flotante en toda página) — vedado al trabajo social
    Route::post('/escanear/registrar', [EscanearController::class, 'registrar'])
        ->middleware('can:escanear')
        ->name('escanear.registrar');

    // Feeds de los concentrados (DataTables)
    Route::get('/datatables/ninos', [NinoController::class, 'datatable'])->name('ninos.datatable');
    Route::get('/datatables/acompanantes', [AcompananteController::class, 'datatable'])->name('acompanantes.datatable');
    Route::get('/datatables/servicios', [RegistroController::class, 'serviciosDatatable'])->name('servicios.datatable');
    Route::get('/datatables/servicios/{servicio}', [RegistroController::class, 'servicioDatatable'])
        ->whereIn('servicio', ['lavanderia', 'comedor', 'escuela', 'transporte'])
        ->name('registros.datatable');
    Route::get('/datatables/entradas-salidas', [RegistroController::class, 'entradasSalidasDatatable'])->name('entradas-salidas.datatable');

    // Administración de cuentas y bitácora de actividad — solo administradores
    Route::redirect('/usuarios', '/administracion'); // la URL vieja sigue funcionando
    Route::middleware('master')->prefix('administracion')->name('administracion.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/actividad', [UserController::class, 'actividad'])->name('actividad');
        Route::get('/reportes/datos', [ReporteController::class, 'datos'])->name('reportes.datos');
        Route::get('/reportes/excel', [ReporteController::class, 'excel'])->name('reportes.excel');
        Route::patch('/{usuario}/estado', [UserController::class, 'toggleActivo'])->name('toggle');
        Route::patch('/{usuario}/password', [UserController::class, 'resetPassword'])->name('password');
    });
});
