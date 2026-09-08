<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Municipio extends Model
{
    use HasFactory;

    protected $table = 'municipio';

    protected $fillable = ['estado_id', 'municipio'];

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }
}
