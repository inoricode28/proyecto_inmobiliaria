<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CronogramaCuotaInicial extends Model
{
    use HasFactory;

    protected $table = 'cronograma_cuota_inicial';

    protected $fillable = [
        'separacion_id',
        'proforma_id', // Agregado para cuotas temporales
        'fecha_pago',
        'monto',
        'tipo',
        'tipo_cuota_id',
        'estado_id',
        'observaciones',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'monto' => 'decimal:2',
    ];

    /**
     * Relación con Separacion
     */
    public function separacion(): BelongsTo
    {
        return $this->belongsTo(Separacion::class);
    }

    /**
     * Relación con TipoCuota
     */
    public function tipoCuota(): BelongsTo
    {
        return $this->belongsTo(TipoCuota::class, 'tipo_cuota_id');
    }

    /**
     * Relación con EstadoCuota
     */
    public function estadoCuota(): BelongsTo
    {
        return $this->belongsTo(EstadoCuota::class, 'estado_id');
    }

    /**
     * Usuario que creó el registro
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Usuario que actualizó el registro
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Tipos de cuota disponibles (usando la tabla)
     */
    public static function getTiposCuota(): array
    {
        return TipoCuota::activos()->pluck('nombre', 'id')->toArray();
    }

    /**
     * Estados de cuota disponibles (usando la tabla)
     */
    public static function getEstados(): array
    {
        return EstadoCuota::activos()->pluck('nombre', 'id')->toArray();
    }
}