<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Separacion extends Model
{
    protected $table = 'separaciones';

    protected $fillable = [
        'proforma_id',
        'tipo_separacion',
        'numero_partida',
        'lugar_partida',
        'co_propietario_porcentaje',
        'ocupacion_id',
        'profesion_id',
        'puesto',
        'categoria_id',
        'ruc',
        'empresa',
        'pep',
        'fecha_pep',
        'direccion_laboral',
        'urbanizacion',
        'departamento_ubigeo_id',
        'provincia_id',
        'distrito_id',
        'telefono1',
        'telefono2',
        'antiguedad_laboral',
        'ingresos',
        'saldo_a_financiar',
        'fecha_vencimiento',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'fecha_pep' => 'date',
        'fecha_vencimiento' => 'date',
    ];
    
    public function proforma(): BelongsTo
    {
        return $this->belongsTo(Proforma::class);
    }

    public function ocupacion(): BelongsTo
    {
        return $this->belongsTo(Ocupacion::class);
    }

    public function profesion(): BelongsTo
    {
        return $this->belongsTo(Profesion::class);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
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

    public function notariaKardex()
    {
        return $this->hasOne(NotariaKardex::class);
    }

    public function cartaFianza()
    {
        return $this->hasOne(CartaFianza::class);
    }

    public function banco()
    {
        return $this->belongsTo(Banco::class);
    }

    public function venta()
    {
        return $this->hasOne(Venta::class);
    }

    public function tieneVenta()
    {
        return $this->venta()->exists();
    }

}
