<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComoSeEntero extends Model
{
    protected $table = 'como_se_entero';

    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    public $timestamps = false;

    // Relación con Prospectos
    public function prospectos()
    {
        return $this->hasMany(Prospecto::class);
    }
}