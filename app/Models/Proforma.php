<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class Proforma extends Model
{
    //use SoftDeletes;

    protected $fillable = [
        'prospecto_id',
        'tipo_documento_id',
        'numero_documento',
        'nombres',
        'ape_paterno',
        'ape_materno',
        'razon_social',
        'genero_id',
        'fecha_nacimiento',
        'nacionalidad_id',
        'estado_civil_id',
        'grado_estudio_id',
        'telefono_casa',
        'celular',
        'email',
        'direccion',
        'departamento_ubigeo_id',
        'provincia_id',
        'distrito_id',
        'direccion_adicional',
        'proyecto_id',
        'departamento_id',
        'precio_venta',
        'descuento',
        'monto_separacion',
        'monto_cuota_inicial',
        'fecha_vencimiento',
        'observaciones',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_vencimiento' => 'date',
    ];
    
    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class);
    }

    public function genero()
    {
        return $this->belongsTo(Genero::class);
    }

    public function nacionalidad()
    {
        return $this->belongsTo(Nacionalidad::class);
    }

    public function estadoCivil()
    {
        return $this->belongsTo(EstadoCivil::class);
    }

    public function gradoEstudio()
    {
        return $this->belongsTo(GradoEstudio::class);
    }

    public function ubigeoDepartamento()
    {
        return $this->belongsTo(DepartamentoUbigeo::class, 'departamento_ubigeo_id');
    }

    public function ubigeoProvincia()
    {
        return $this->belongsTo(Provincia::class, 'provincia_id');
    }

    public function ubigeoDistrito()
    {
        return $this->belongsTo(Distrito::class, 'distrito_id');
    }

    public function prospecto()
    {
        return $this->belongsTo(Prospecto::class);
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    /**
     * Relación con múltiples inmuebles a través de la tabla intermedia
     */
    public function inmuebles()
    {
        return $this->hasMany(ProformaInmueble::class)->ordenado();
    }

    /**
     * Obtener el inmueble principal (para compatibilidad con código existente)
     */
    public function inmueblePrincipal()
    {
        return $this->hasOne(ProformaInmueble::class)->principal();
    }

    /**
     * Obtener inmuebles adicionales
     */
    public function inmueblesAdicionales()
    {
        return $this->hasMany(ProformaInmueble::class)->adicionales()->ordenado();
    }

    public function documentos()
    {
        return $this->hasMany(ProformaDocumento::class);
    }
    
    public function separacion()
    {
        return $this->hasOne(Separacion::class);
    }
    
    public function getCodigoFormateadoAttribute()
    {
        return 'PRO' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }
}
