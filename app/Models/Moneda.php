<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Moneda extends Model
{
    protected $table = 'moneda';

    protected $fillable = [
        'nombre',
    ];

    public $timestamps = false;

    /**
     * Relación con departamentos
     */
    public function departamentos()
    {
        return $this->hasMany(Departamento::class, 'moneda_id');
    }

    /**
     * Relación con pagos de separación
     */
    public function pagosSeparacion()
    {
        return $this->hasMany(PagoSeparacion::class, 'moneda_id');
    }
}
