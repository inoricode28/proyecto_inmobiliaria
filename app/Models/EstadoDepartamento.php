<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoDepartamento extends Model
{
    protected $table = 'estados_departamento';

    protected $fillable = [
        'nombre',
        'descripcion',
        'color',
        'is_default',
    ];

    public $timestamps = false;

    public function departamentos()
    {
        return $this->hasMany(Departamento::class, 'estado_departamento_id');
    }

    // Opcional: Scope para obtener el estado por defecto
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // Opcional: Mutador para asegurar el formato del color
    public function setColorAttribute($value)
    {
        $this->attributes['color'] = str_starts_with($value, '#') ? $value : '#'.$value;
    }
}
