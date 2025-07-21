<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Prospecto extends Model
{
    use SoftDeletes;
    protected $with = ['tareaAsignada.usuarioAsignado'];


    protected $table = 'prospectos';

    protected $fillable = [
        'fecha_registro',
        'tipo_documento_id',
        'numero_documento',
        'nombres',
        'ape_paterno',
        'ape_materno',
        'razon_social',
        'celular',
        'correo_electronico',
        'proyecto_id',
        'tipo_inmueble_id',
        'forma_contacto_id',
        'como_se_entero_id',
        'tipo_gestion_id',
        'created_by',
        'updated_by'
    ];

    protected $dates = [
        'fecha_registro',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Relación con TipoDocumento
    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class);
    }

    // Relación con Proyecto
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    // Relación con TipoInmueble
    public function tipoInmueble()
    {
        return $this->belongsTo(TipoInmueble::class);
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

    // Relación con FormaContacto
    public function formaContacto()
    {
        return $this->belongsTo(FormaContacto::class);
    }

    // Relación con ComoSeEntero
    public function comoSeEntero()
    {
        return $this->belongsTo(ComoSeEntero::class);
    }

    // Relación con TipoGestion
    public function tipoGestion()
    {
        return $this->belongsTo(TipoGestion::class);
    }

    // Relación con Tareas
    public function tareas()
    {
        return $this->hasMany(Tarea::class);
    }

    public function nivelInteres()
    {
        return $this->belongsTo(NivelInteres::class);
    }

    public function tareaAsignada()
    {
        return $this->hasOne(Tarea::class)->latestOfMany(); // O firstOfMany() si prefieres la más antigua
    }

    public function usuarioAsignado()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
