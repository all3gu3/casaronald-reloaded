<?php

namespace App\Models;

use App\Enums\Servicio;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistroServicio extends Model
{
    use HasFactory;

    protected $table = 'registros_servicios';

    protected $fillable = ['nino_id', 'qr', 'servicio'];

    protected function casts(): array
    {
        return [
            'servicio' => Servicio::class,
        ];
    }

    public function nino(): BelongsTo
    {
        return $this->belongsTo(Nino::class, 'nino_id');
    }
}
