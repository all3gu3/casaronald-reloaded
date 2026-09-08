<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nino extends Model
{
    use HasFactory;

    protected $table = 'nino';

    protected $fillable = [
        'qr',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'fecha_nacimiento',
        'sexo',
        'foto',
        'calle',
        'numero',
        'colonia',
        'localidad',
        'municipio',
        'cp',
        'primer_telefono',
        'segundo_telefono',
        'dialecto',
        'diagnostico',
        'medico',
        'alerg_alimentos',
        'alerg_medicamentos',
        'servicio',
        'estatus_estancia',
        'fecha_solicitud',
        'fecha_ingreso',
        'fecha_salida',
        'observaciones',
        'trabajador_social_id',
        'tipo_dieta_id',
        'hospital_id',
        'escolaridad_id',
        'clasificacion_social_id',
        'zona_id',
        'salario_minimo_id',
        'pais_id',
        'estado_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'fecha_solicitud' => 'date',
            'fecha_ingreso' => 'date',
            'fecha_salida' => 'date',
        ];
    }

    /** Edad en años, calculada — ya no se almacena un dato que caduca. */
    protected function edad(): Attribute
    {
        return Attribute::get(fn () => $this->fecha_nacimiento?->age);
    }

    public function nombreCompleto(): string
    {
        return trim("{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}");
    }

    public function acompanantes(): HasMany
    {
        return $this->hasMany(Acompanante::class, 'nino_id');
    }

    public function registrosServicios(): HasMany
    {
        return $this->hasMany(RegistroServicio::class, 'nino_id');
    }

    public function entradasSalidas(): HasMany
    {
        return $this->hasMany(EntradaSalida::class, 'nino_id');
    }

    public function registrosOperativos(): HasMany
    {
        return $this->hasMany(RegistroOperativo::class, 'nino_id');
    }

    public function tiposTratamiento(): BelongsToMany
    {
        return $this->belongsToMany(TipoTratamiento::class, 'nino_tipo_tratamiento')
            ->withTimestamps();
    }

    public function trabajadorSocial(): BelongsTo
    {
        return $this->belongsTo(TrabajadorSocial::class, 'trabajador_social_id');
    }

    public function tipoDieta(): BelongsTo
    {
        return $this->belongsTo(TipoDieta::class, 'tipo_dieta_id');
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class, 'hospital_id');
    }

    public function escolaridad(): BelongsTo
    {
        return $this->belongsTo(Escolaridad::class, 'escolaridad_id');
    }

    public function clasificacionSocial(): BelongsTo
    {
        return $this->belongsTo(ClasificacionSocial::class, 'clasificacion_social_id');
    }

    public function zona(): BelongsTo
    {
        return $this->belongsTo(Zona::class, 'zona_id');
    }

    public function salarioMinimo(): BelongsTo
    {
        return $this->belongsTo(SalarioMinimo::class, 'salario_minimo_id');
    }

    public function pais(): BelongsTo
    {
        return $this->belongsTo(Pais::class, 'pais_id');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }
}
