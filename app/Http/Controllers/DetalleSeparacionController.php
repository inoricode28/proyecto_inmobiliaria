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
            'separacion',
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
        
        if (!$proforma->separacion) {
            abort(404, 'No se encontró información de separación para esta proforma');
        }

        $departamento = $proforma->departamento;
        $separacion = $proforma->separacion;

        return view('detalle-separacion', compact('departamento', 'separacion', 'proforma'));
    }
}