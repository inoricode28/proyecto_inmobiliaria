<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoGestion extends Model
{
    protected $table = 'tipos_gestion';

    protected $fillable = [
        'nombre',
        'descripcion',
        'color',
        'orden'
    ];

    public $timestamps = false;

    // Relación con Prospectos
    public function prospectos()
    {
        return $this->hasMany(Prospecto::class);
    }
}