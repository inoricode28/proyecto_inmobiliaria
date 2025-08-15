<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use App\Models\EstadoDepartamento;
use App\Models\Departamento;
use App\Models\Entrega; // Agregar esta línea

class Venta extends Model
{
    protected $table = 'ventas';

    protected $fillable = [
        'separacion_id',
        'fecha_entrega_inicial',
        'fecha_venta',
        'fecha_preminuta',
        'fecha_minuta',
        'estado',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'fecha_entrega_inicial' => 'date',
        'fecha_venta' => 'date',
        'fecha_preminuta' => 'date',
        'fecha_minuta' => 'date',
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

        // NUEVO: Crear registro de entrega automáticamente después de crear la venta
        static::created(function ($model) {
            if ($model->fecha_entrega_inicial && $model->separacion && $model->separacion->proforma) {
                Entrega::create([
                    'venta_id' => $model->id,
                    'prospecto_id' => $model->separacion->proforma->prospecto_id,
                    'departamento_id' => $model->separacion->proforma->departamento_id,
                    'fecha_entrega' => $model->fecha_entrega_inicial, // Aquí se registra la fecha
                    'descripcion' => 'Entrega creada automáticamente desde la venta',
                    'created_by' => Auth::id(),
                ]);
            }
        });

        static::deleting(function ($model) {
            // Cambiar el estado del departamento de vuelta a 'Separacion'
            if ($model->separacion && $model->separacion->proforma && $model->separacion->proforma->departamento) {
                $estadoSeparacion = EstadoDepartamento::where('nombre', 'Separacion')->first();
                if ($estadoSeparacion) {
                    $departamento = $model->separacion->proforma->departamento;
                    $departamento->estado_departamento_id = $estadoSeparacion->id;
                    $departamento->save();
                }
            }
        });
    }

    // Relación con Separacion
    public function separacion(): BelongsTo
    {
        return $this->belongsTo(Separacion::class);
    }

    // Relación con Usuario Creador
    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relación con Usuario Actualizador
    public function actualizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Acceso a datos del cliente a través de separación
    public function getClienteDataAttribute()
    {
        if (!$this->separacion || !$this->separacion->proforma) {
            return null;
        }

        $proforma = $this->separacion->proforma;

        return [
            'tipo_documento' => $proforma->tipoDocumento?->nombre,
            'numero_documento' => $proforma->numero_documento,
            'nombres' => $proforma->nombres,
            'ape_paterno' => $proforma->ape_paterno,
            'ape_materno' => $proforma->ape_materno,
            'razon_social' => $proforma->razon_social,
            'genero' => $proforma->genero?->nombre,
            'fecha_nacimiento' => $proforma->fecha_nacimiento,
            'nacionalidad' => $proforma->nacionalidad?->nombre,
            'estado_civil' => $proforma->estadoCivil?->nombre,
            'grado_estudio' => $proforma->gradoEstudio?->nombre,
            'telefono_casa' => $proforma->telefono_casa,
            'celular' => $proforma->celular,
            'email' => $proforma->email,
            'direccion' => $proforma->direccion,
            'departamento_ubigeo' => $proforma->ubigeoDepartamento?->nombre,
            'provincia' => $proforma->ubigeoProvincia?->nombre,
            'distrito' => $proforma->ubigeoDistrito?->nombre,
            'direccion_adicional' => $proforma->direccion_adicional,
        ];
    }

    // Acceso a datos de separación
    public function getSeparacionDataAttribute()
    {
        if (!$this->separacion) {
            return null;
        }

        return [
            'tipo_separacion' => $this->separacion->tipo_separacion,
            'numero_partida' => $this->separacion->numero_partida,
            'lugar_partida' => $this->separacion->lugar_partida,
            'co_propietario_porcentaje' => $this->separacion->co_propietario_porcentaje,
            'ocupacion' => $this->separacion->ocupacion?->nombre,
            'profesion' => $this->separacion->profesion?->nombre,
            'puesto' => $this->separacion->puesto,
            'categoria' => $this->separacion->categoria?->nombre,
            'ruc' => $this->separacion->ruc,
            'empresa' => $this->separacion->empresa,
            'pep' => $this->separacion->pep,
            'fecha_pep' => $this->separacion->fecha_pep,
            'direccion_laboral' => $this->separacion->direccion_laboral,
            'urbanizacion' => $this->separacion->urbanizacion,
            'departamento_ubigeo' => $this->separacion->ubigeoDepartamento?->nombre,
            'provincia' => $this->separacion->ubigeoProvincia?->nombre,
            'distrito' => $this->separacion->ubigeoDistrito?->nombre,
            'telefono1' => $this->separacion->telefono1,
            'telefono2' => $this->separacion->telefono2,
            'antiguedad_laboral' => $this->separacion->antiguedad_laboral,
            'ingresos' => $this->separacion->ingresos,
            'saldo_a_financiar' => $this->separacion->saldo_a_financiar,
        ];
    }

    // Acceso a datos del inmueble a través de separación
    public function getInmuebleDataAttribute()
    {
        if (!$this->separacion || !$this->separacion->proforma) {
            return null;
        }

        $proforma = $this->separacion->proforma;

        return [
            'proyecto' => $proforma->proyecto?->nombre,
            'departamento' => $proforma->departamento?->nombre,
            'monto_separacion' => $proforma->monto_separacion,
            'monto_cuota_inicial' => $proforma->monto_cuota_inicial,
            'saldo_a_financiar' => $this->separacion->saldo_a_financiar,
        ];
    }

    // Acceso a datos de notaría y kardex
    public function getNotariaKardexDataAttribute()
    {
        if (!$this->separacion || !$this->separacion->notariaKardex) {
            return null;
        }

        $notaria = $this->separacion->notariaKardex;

        return [
            'notaria' => $notaria->notaria,
            'kardex' => $notaria->kardex,
            'banco' => $notaria->banco,
            'observaciones' => $notaria->observaciones,
        ];
    }

    // Acceso a datos de carta fianza
    public function getCartaFianzaDataAttribute()
    {
        if (!$this->separacion || !$this->separacion->cartaFianza) {
            return null;
        }

        $carta = $this->separacion->cartaFianza;

        return [
            'banco' => $carta->banco,
            'monto' => $carta->monto,
            'fecha_emision' => $carta->fecha_emision,
            'fecha_vencimiento' => $carta->fecha_vencimiento,
            'observaciones' => $carta->observaciones,
        ];
    }

    // Acceso a observaciones de la proforma
    public function getObservacionesDataAttribute()
    {
        if (!$this->separacion || !$this->separacion->proforma) {
            return null;
        }

        return $this->separacion->proforma->observaciones;
    }
    // Agregar esta relación al modelo Venta existente
    public function entregas()
    {
        return $this->hasMany(Entrega::class);
    }

    public function entrega()
    {
        return $this->hasOne(Entrega::class);
    }
}