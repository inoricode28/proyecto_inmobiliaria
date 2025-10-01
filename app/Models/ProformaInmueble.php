<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProformaInmueble extends Model
{
    protected $table = 'proforma_inmuebles';

    protected $fillable = [
        'proforma_id',
        'departamento_id',
        'precio_lista',
        'precio_venta',
        'descuento',
        'monto_separacion',
        'monto_cuota_inicial',
        'orden',
        'es_principal',
    ];

    protected $casts = [
        'precio_lista' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'descuento' => 'decimal:2',
        'monto_separacion' => 'decimal:2',
        'monto_cuota_inicial' => 'decimal:2',
        'orden' => 'integer',
        'es_principal' => 'boolean',
    ];

    /**
     * Relación con Proforma
     */
    public function proforma(): BelongsTo
    {
        return $this->belongsTo(Proforma::class);
    }

    /**
     * Relación con Departamento (Inmueble)
     */
    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }

    /**
     * Scope para obtener solo inmuebles principales
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
     * Calcular el precio venta basado en precio lista y descuento
     */
    public function calcularPrecioVenta()
    {
        if ($this->precio_lista && $this->descuento) {
            $montoDescuento = $this->precio_lista * ($this->descuento / 100);
            return $this->precio_lista - $montoDescuento;
        }
        
        return $this->precio_lista;
    }

    /**
     * Calcular la cuota inicial (10% del precio venta)
     */
    public function calcularCuotaInicial()
    {
        $precioVenta = $this->precio_venta ?: $this->calcularPrecioVenta();
        return $precioVenta * 0.10;
    }
}