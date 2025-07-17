<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotariaKardex extends Model
{
    protected $table = 'notaria_kardex';

    protected $fillable = [
        'separacion_id',
        'notaria',
        'responsable',
        'direccion',
        'email',
        'celular',
        'telefono',
        'numero_kardex',
        'oficina',
        'numero_registro',
        'agencia',
        'asesor',
        'telefonos',
        'correos',
        'fecha_vencimiento_carta',
        'fecha_escritura_publica',
        'penalidad_entrega',
    ];

    public function separacion(): BelongsTo
    {
        return $this->belongsTo(Separacion::class);
    }
}
