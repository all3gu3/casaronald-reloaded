<?php

namespace App\Models;

use App\Enums\Accion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Bitácora de actividad de las cuentas: inicios de sesión, escaneos,
 * descargas, altas y ediciones. Solo se inserta y se consulta — un registro
 * de auditoría nunca se edita (por eso no hay updated_at).
 */
class RegistroActividad extends Model
{
    const UPDATED_AT = null;

    protected $table = 'registro_actividad';

    protected $fillable = ['user_id', 'accion', 'detalle'];

    protected function casts(): array
    {
        return [
            'accion' => Accion::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Anota una acción del usuario autenticado (sin sesión no anota nada). */
    public static function anotar(Accion $accion, ?string $detalle = null): void
    {
        if (Auth::id() === null) {
            return;
        }

        static::create([
            'user_id' => Auth::id(),
            'accion' => $accion,
            'detalle' => $detalle,
        ]);
    }
}
