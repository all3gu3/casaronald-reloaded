<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalarioMinimo extends Model
{
    use HasFactory;

    protected $table = 'salario_minimo';

    protected $fillable = ['salario_minimo'];
}
