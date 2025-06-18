<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Edificio extends Model
{
    protected $table = 'edificios';

    protected $fillable = [
        'proyecto_id',
        'nombre',
        'descripcion',
        'cantidad_pisos',
        'cantidad_departamentos',
        'fecha_inicio',
        'fecha_entrega',
    ];

    public $timestamps = true;

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function departamentos()
    {
        return $this->hasMany(Departamento::class);
    }
}
