<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrabajadorSocial extends Model
{
    use HasFactory;

    protected $table = 'trabajador_social';

    protected $fillable = ['trabajador_social'];
}
