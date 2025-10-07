<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeparacionInmueble extends Model
{
    use HasFactory;

    protected $table = 'separacion_inmuebles';

    protected $fillable = [
        'separacion_id',
        'departamento_id',
        'precio_lista',
        'precio_venta',
        'descuento',
        'monto_separacion',
        'monto_cuota_inicial',
        'orden',
        'es_principal'
    ];

    protected $casts = [
        'precio_lista' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'descuento' => 'decimal:2',
        'monto_separacion' => 'decimal:2',
        'monto_cuota_inicial' => 'decimal:2',
        'es_principal' => 'boolean'
    ];

    /**
     * Relación con la separación
     */
    public function separacion()
    {
        return $this->belongsTo(Separacion::class);
    }

    /**
     * Relación con el departamento
     */
    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    /**
     * Scope para obtener solo el inmueble principal
     */
    public function scopePrincipal($query)
    {
        return $query->where('es_principal', true);
    }

    /**
     * Scope para obtener inmuebles adicionales
     */
    public function scopeAdicionales($query)
    {
        return $query->where('es_principal', false);
    }

    /**
     * Scope para ordenar por orden
     */
    public function scopeOrdenado($query)
    {
        return $query->orderBy('orden');
    }

    /**
     * Accessor para obtener el precio con descuento aplicado
     */
    public function getPrecioConDescuentoAttribute()
    {
        if ($this->descuento && $this->precio_venta) {
            return $this->precio_venta * (1 - ($this->descuento / 100));
        }
        return $this->precio_venta;
    }

    /**
     * Accessor para obtener el monto del descuento
     */
    public function getMontoDescuentoAttribute()
    {
        if ($this->descuento && $this->precio_venta) {
            return $this->precio_venta * ($this->descuento / 100);
        }
        return 0;
    }
}