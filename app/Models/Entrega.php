<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Entrega extends Model
{
    protected $table = 'entregas';

    protected $fillable = [
        'venta_id',
        'prospecto_id',
        'departamento_id',
        'fecha_entrega',
        'fecha_garantia_acabados',
        'fecha_garantia_vicios_ocultos',
        'descripcion',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'fecha_entrega' => 'date',
        'fecha_garantia_acabados' => 'date',
        'fecha_garantia_vicios_ocultos' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = Auth::id();
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });
    }

    // Relaciones
    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function prospecto(): BelongsTo
    {
        return $this->belongsTo(Prospecto::class);
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Accesor para obtener el nombre completo del prospecto
    public function getNombreCompletoProspectoAttribute()
    {
        if ($this->prospecto) {
            $tieneNombre = $this->prospecto->nombres && $this->prospecto->ape_paterno;
            return $tieneNombre
                ? $this->prospecto->nombres . ' ' . $this->prospecto->ape_paterno . ' ' . ($this->prospecto->ape_materno ?? '')
                : ($this->prospecto->razon_social ?? '-');
        }
        return '-';
    }
}