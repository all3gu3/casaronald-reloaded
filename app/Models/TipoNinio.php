<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoNinio extends Model
{
    use HasFactory;

    protected $table = 'tipo_ninio';

    protected $fillable = ['tipo_ninio'];
}
