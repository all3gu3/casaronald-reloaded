<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntradaSalida extends Model
{
    use HasFactory;

    protected $table = 'entradas_salidas';

    protected $fillable = ['nino_id', 'qr', 'entrada', 'salida'];

    protected function casts(): array
    {
        return [
            'entrada' => 'datetime',
            'salida' => 'datetime',
        ];
    }

    public function nino(): BelongsTo
    {
        return $this->belongsTo(Nino::class, 'nino_id');
    }

    public function scopeAbiertas(Builder $query): Builder
    {
        return $query->whereNull('salida');
    }

    /** El registro abierto (entrada sin salida) más reciente del niño, si existe. */
    public static function abiertaDe(Nino $nino): ?self
    {
        return static::query()
            ->where('nino_id', $nino->id)
            ->abiertas()
            ->latest('entrada')
            ->first();
    }
}
