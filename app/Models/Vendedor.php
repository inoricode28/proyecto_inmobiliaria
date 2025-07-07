<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendedor extends Model
{
    use SoftDeletes;

    protected $table = 'vendedores';

    protected $fillable = [
        'user_id',
        'tipo_documento_id',
        'numero_documento',
        'nombre',
        'telefono',
        'email',
        'estado_id',
        'fecha_ingreso',
        'fecha_egreso',
        'proyecto_id',
        'comision',
        'perfil',
        'created_by',
        'updated_by'
    ];

    protected $dates = [
        'fecha_ingreso',
        'fecha_egreso',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'comision' => 'decimal:2'
    ];

    // Relación con User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con TipoDocumento
    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class);
    }

    // Relación con Estado
    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    // Relación con Proyecto
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    // Relación con Usuario Creador
    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relación con Usuario Actualizador
    public function actualizador()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Accesor para nombre completo
    public function getNombreCompletoAttribute()
    {
        return $this->nombre;
    }

    // Accesor para estado actual
    public function getEstadoActualAttribute()
    {
        return optional($this->estado)->nombre ?? 'Sin estado';
    }
}