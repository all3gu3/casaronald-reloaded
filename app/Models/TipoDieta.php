<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDieta extends Model
{
    use HasFactory;

    protected $table = 'tipo_dieta';

    protected $fillable = ['tipo_dieta'];
}
