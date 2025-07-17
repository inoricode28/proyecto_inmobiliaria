<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProformaDocumento extends Model
{
    protected $fillable = [
        'proforma_id',
        'nombre',
        'ruta',
    ];

    public function proforma()
    {
        return $this->belongsTo(Proforma::class);
    }
}
