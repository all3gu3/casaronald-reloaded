<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClasificacionSocial extends Model
{
    use HasFactory;

    protected $table = 'clasificacion_social';

    protected $fillable = ['clasificacion_social'];
}
