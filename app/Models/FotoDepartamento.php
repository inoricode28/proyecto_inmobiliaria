<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoDepartamento extends Model
{
    protected $table = 'foto_departamentos';

    protected $fillable = [
        'proyecto_id',
        'edificio_id',
        'departamento_id',
        'imagen',
        'imagen_adicional',
    ];

    public $timestamps = true;

    // Relaciones
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function edificio()
    {
        return $this->belongsTo(Edificio::class);
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }
}