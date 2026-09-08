<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EdoSalud extends Model
{
    use HasFactory;

    protected $table = 'edo_salud';

    protected $fillable = ['edo_salud'];
}
