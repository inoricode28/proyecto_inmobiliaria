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



    // TipoGestion.php
public function prospectos()
{
    return $this->hasMany(\App\Models\Prospecto::class, 'tipo_gestion_id');
}


}
