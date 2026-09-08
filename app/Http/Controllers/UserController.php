<?php

namespace App\Http\Controllers;

use App\Enums\Servicio;
use App\Http\Requests\StoreUserRequest;
use App\Models\RegistroActividad;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

/**
 * Administración de cuentas — solo administradores (middleware `master`).
 * Las cuentas nunca se eliminan: se desactivan, para conservar la trazabilidad.
 */
class UserController extends Controller
{
    public function index(): View
    {
        return view('administracion.index', [
            'usuarios' => User::orderBy('name')->get(),
            'servicios' => Servicio::cases(),
        ]);
    }

    /**
     * Perfil de una cuenta: datos generales y su propia bitácora de actividad.
     * Sin parámetro muestra el perfil del usuario autenticado; con parámetro,
     * solo el propio usuario o un administrador (gate `manage-users`).
     */
    public function perfil(Request $request, ?User $usuario = null): View
    {
        $usuario ??= $request->user();

        if (! $usuario->is($request->user())) {
            Gate::authorize('manage-users');
        }

        return view('administracion.perfil', [
            'usuario' => $usuario,
            'actividad' => RegistroActividad::where('user_id', $usuario->id)
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->paginate(15),
        ]);
    }

    /** Bitácora de actividad de las cuentas, con filtro opcional por usuario. */
    public function actividad(Request $request): JsonResponse
    {
        $query = RegistroActividad::query()->with('user')->select('registro_actividad.*');

        if ($request->filled('usuario')) {
            $query->where('user_id', $request->integer('usuario'));
        }

        return DataTables::eloquent($query)
            ->addColumn('fecha', fn (RegistroActividad $registro) => $registro->created_at->legible())
            ->addColumn('usuario', fn (RegistroActividad $registro) => $registro->user->name)
            ->addColumn('accion_etiqueta', fn (RegistroActividad $registro) => $registro->accion->etiqueta())
            ->orderColumn('fecha', 'created_at $1')
            ->toJson();
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::create([
            ...$request->validated(),
            'password' => Hash::make($request->validated('password')),
        ]);

        return redirect()->route('administracion.index')
            ->with('exito', 'Cuenta creada para '.$request->validated('name').'.');
    }

    public function toggleActivo(Request $request, User $usuario): RedirectResponse
    {
        if ($usuario->is($request->user())) {
            return redirect()->route('administracion.index')
                ->withErrors(['usuario' => 'No puedes desactivar tu propia cuenta.']);
        }

        $usuario->update(['is_active' => ! $usuario->is_active]);

        return redirect()->route('administracion.index')
            ->with('exito', $usuario->is_active
                ? 'Cuenta de '.$usuario->name.' reactivada.'
                : 'Cuenta de '.$usuario->name.' desactivada.');
    }

    public function resetPassword(Request $request, User $usuario): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', Password::min(8)],
        ]);

        $usuario->update(['password' => Hash::make($validated['password'])]);

        return redirect()->route('administracion.index')
            ->with('exito', 'Contraseña restablecida para '.$usuario->name.'.');
    }
}
