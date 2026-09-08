<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Acompanante extends Model
{
    use HasFactory;

    protected $table = 'acompanante';

    protected $fillable = [
        'nino_id',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'edad',
        'sexo',
        'identificacion',
        'tratamiento',
        'fecha_registro',
        'observaciones',
        'foto',
        'parentesco_id',
        'escolaridad_id',
        'edo_salud_id',
        'ocupacion_id',
        'trabaja',
        'licencia_goce_sueldo',
        'seguro_medico',
        'casa_propia',
        'asistencia_financiera',
        'renta_mensualidad',
        'dependientes_economicos',
        'ingreso_mensual',
    ];

    protected function casts(): array
    {
        return [
            'fecha_registro' => 'date',
            'trabaja' => 'boolean',
            'licencia_goce_sueldo' => 'boolean',
            'seguro_medico' => 'boolean',
            'casa_propia' => 'boolean',
            'asistencia_financiera' => 'boolean',
            'renta_mensualidad' => 'integer',
            'dependientes_economicos' => 'integer',
            'ingreso_mensual' => 'integer',
        ];
    }

    public function nombreCompleto(): string
    {
        return trim("{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}");
    }

    public function nino(): BelongsTo
    {
        return $this->belongsTo(Nino::class, 'nino_id');
    }

    public function parentesco(): BelongsTo
    {
        return $this->belongsTo(Parentesco::class, 'parentesco_id');
    }

    public function escolaridad(): BelongsTo
    {
        return $this->belongsTo(Escolaridad::class, 'escolaridad_id');
    }

    public function edoSalud(): BelongsTo
    {
        return $this->belongsTo(EdoSalud::class, 'edo_salud_id');
    }

    public function ocupacion(): BelongsTo
    {
        return $this->belongsTo(Ocupacion::class, 'ocupacion_id');
    }
}
