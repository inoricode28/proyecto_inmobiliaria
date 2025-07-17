<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartaFianza extends Model
{
    protected $table = 'cartas_fianza';

    protected $fillable = [
        'separacion_id',
        'banco_id',
        'monto',
        'numero_carta',
    ];

    public function separacion(): BelongsTo
    {
        return $this->belongsTo(Separacion::class);
    }

    public function banco(): BelongsTo
    {
        return $this->belongsTo(Banco::class);
    }
}

