<?php

namespace App\Http\Controllers;

use App\Models\Proforma;
use App\Models\Separacion;
use Illuminate\Http\Request;

class DetalleSeparacionController extends Controller
{
    public function show($proforma_id)
    {
        // Buscar la proforma con todas las relaciones necesarias
        $proforma = Proforma::with([
            'separacion.inmuebles.departamento.proyecto',
            'separacion.inmuebles.departamento.tipoInmueble',
            'separacion.inmuebles.departamento.estadoDepartamento',
            'separacion.inmuebles.departamento.tipoFinanciamiento',
            'proformaInmuebles.departamento.proyecto',
            'proformaInmuebles.departamento.tipoInmueble',
            'proformaInmuebles.departamento.estadoDepartamento',
            'proformaInmuebles.departamento.tipoFinanciamiento',
            'departamento.estadoDepartamento',
            'departamento.tipoFinanciamiento',
            'departamento.proyecto',
            'prospecto',
            'tipoDocumento',
            'genero',
            'estadoCivil',
            'gradoEstudio',
            'nacionalidad',
            'separacion.notariaKardex',
            'separacion.cartaFianza'
        ])->find($proforma_id);
        
        if (!$proforma) {
            abort(404, 'No se encontró la proforma especificada');
        }
        
        // Obtener la separación y los inmuebles asociados
        $separacion = $proforma->separacion;
        $inmuebles = collect();
        
        if ($separacion && $separacion->inmuebles->count() > 0) {
            // Si hay separación con múltiples inmuebles, usar esos
            $inmuebles = $separacion->inmuebles;
        } elseif ($proforma->proformaInmuebles && $proforma->proformaInmuebles->count() > 0) {
            // Si no hay separación pero hay múltiples inmuebles en la proforma, usar esos
            $inmuebles = $proforma->proformaInmuebles;
        } elseif ($proforma->departamento) {
            // Fallback: usar el departamento principal
            $inmuebles = collect([(object)[
                'departamento' => $proforma->departamento,
                'precio_venta' => $proforma->departamento->Precio_lista ?? 0,
                'descuento' => 0,
                'monto_separacion' => 0,
                'monto_cuota_inicial' => 0,
                'saldo_financiar' => $proforma->departamento->Precio_lista ?? 0
            ]]);
        }
        
        // Para compatibilidad con vistas existentes, mantener $departamento como el principal
        $departamento = $proforma->departamento;

        return view('detalle-separacion', compact('departamento', 'separacion', 'proforma', 'inmuebles'));
    }
}