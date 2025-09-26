<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CronogramaSaldoFinanciar extends Model
{
    use HasFactory;

    protected $table = 'cronogramas_saldo_financiar';

    protected $fillable = [
        'separacion_id',
        'fecha_inicio',
        'monto_total',
        'saldo_financiar',
        'numero_cuotas',
        'tipo_financiamiento_id',
        'banco_id',
        'tipo_comprobante',
        'bono_mivivienda',
        'bono_verde',
        'bono_integrador',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'monto_total' => 'decimal:2',
        'saldo_financiar' => 'decimal:2',
        'bono_mivivienda' => 'boolean',
        'bono_verde' => 'boolean',
        'bono_integrador' => 'boolean',
    ];

    // Relaciones
    public function separacion(): BelongsTo
    {
        return $this->belongsTo(Separacion::class);
    }

    public function tipoFinanciamiento(): BelongsTo
    {
        return $this->belongsTo(TiposFinanciamiento::class, 'tipo_financiamiento_id');
    }

    public function banco(): BelongsTo
    {
        return $this->belongsTo(Banco::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(CronogramaSaldoFinanciarDetalle::class, 'cronograma_sf_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
