<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estado extends Model
{
    use HasFactory;

    protected $table = 'estado';

    protected $fillable = ['pais_id', 'estado'];

    public function pais(): BelongsTo
    {
        return $this->belongsTo(Pais::class, 'pais_id');
    }

    public function municipios(): HasMany
    {
        return $this->hasMany(Municipio::class, 'estado_id');
    }
}
