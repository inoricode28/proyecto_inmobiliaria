<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'departamentos';

    protected $fillable = [
        'proyecto_id',
        'centro_costos', // Añadido porque estaba faltando
        'edificio_id',
        'tipo_inmueble_id',
        'tipo_departamento_id',
        'estado_departamento_id',
        'numero_inicial',
        'numero_final',
        'ficha_indep',
        'num_departamento',
        'num_piso',
        'num_dormitorios',
        'num_bano',
        'num_certificado',
        'bono_techo_propio',
        'num_bono_tp',
        'cantidad_uit',
        'codigo_bancario',
        'codigo_catastral',
        'vista_id',
        'orden',
        'moneda_id', // Cambiado de modena_id a moneda_id
        'precio',
        'Precio_lista',
        'Precio_venta',
        'descuento', // Cambiado de descuent a descuento
        'predio_m2',
        'terreno',
        'techada',
        'construida',
        'terraza',
        'jardin',
        'adicional',
        'vendible',
        'frente',
        'derecha',
        'izquierda',
        'fondo',
        'direccion',
        'observaciones',
        'estado_id',
        'tipos_financiamiento_id',

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

    public function tipoInmueble()
    {
        return $this->belongsTo(TipoInmueble::class, 'tipo_inmueble_id');
    }

    public function tipoDepartamento()
    {
        return $this->belongsTo(TipoDepartamento::class, 'tipo_departamento_id');
    }

    public function vista()
    {
        return $this->belongsTo(Vista::class, 'vista_id');
    }

    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'moneda_id'); // Relación corregida
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function estadoDepartamento()
    {
        return $this->belongsTo(EstadoDepartamento::class, 'estado_departamento_id');
    }
    #Aqui se relaciona el modelo de partamento con el con la tabla de tipo de financiamientro
    public function tipoFinanciamiento()
    {
        return $this->belongsTo(tipoFinanciamiento::class, 'tipos_financiamiento_id');
    }


    public function fotoDepartamentos()
    {
        return $this->hasMany(FotoDepartamento::class, 'departamento_id');
    }

}
