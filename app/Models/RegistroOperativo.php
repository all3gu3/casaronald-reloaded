<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistroOperativo extends Model
{
    use HasFactory;

    protected $table = 'registro_operativo';

    protected $fillable = [
        'nino_id',
        'hospital_id',
        'tipo_ninio_id',
        'tipo_tratamiento_id',
        'tipo_dieta_id',
        'trabajador_social_id',
        'fecha_ingreso',
        'fecha_egreso',
        'medico_atiende',
        'diagnostico',
        'reingreso',
        'ninos_adicionales',
        'habitacion',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_ingreso' => 'date',
            'fecha_egreso' => 'date',
            'reingreso' => 'boolean',
            'ninos_adicionales' => 'integer',
            'habitacion' => 'integer',
        ];
    }

    public function nino(): BelongsTo
    {
        return $this->belongsTo(Nino::class, 'nino_id');
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class, 'hospital_id');
    }

    public function tipoNinio(): BelongsTo
    {
        return $this->belongsTo(TipoNinio::class, 'tipo_ninio_id');
    }

    public function tipoTratamiento(): BelongsTo
    {
        return $this->belongsTo(TipoTratamiento::class, 'tipo_tratamiento_id');
    }

    public function tipoDieta(): BelongsTo
    {
        return $this->belongsTo(TipoDieta::class, 'tipo_dieta_id');
    }

    public function trabajadorSocial(): BelongsTo
    {
        return $this->belongsTo(TrabajadorSocial::class, 'trabajador_social_id');
    }
}
