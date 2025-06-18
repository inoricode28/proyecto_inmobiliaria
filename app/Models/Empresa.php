<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'empresas';

    protected $fillable = [
        'nombre',
        'ruc',
        'direccion',
        'telefono',
        'email',
        'representante_legal',
    ];

    public $timestamps = true;

    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'empresa_constructora_id');
    }
}
