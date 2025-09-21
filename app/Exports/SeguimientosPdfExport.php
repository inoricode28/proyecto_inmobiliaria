<?php

namespace App\Exports;

use App\Models\Tarea;
use Barryvdh\DomPDF\Facade\Pdf;

class SeguimientosPdfExport
{
    protected $query;

    public function __construct($query = null)
    {
        $this->query = $query;
    }

    public function export()
    {
        $query = $this->query ?: Tarea::query();
        
        $seguimientos = $query->with(['prospecto.proyecto', 'prospecto.formaContacto', 'usuarioAsignado'])
            ->get()
            ->map(function ($tarea) {
                return [
                    'id' => $tarea->prospecto->id ?? '',
                    'nombres' => ($tarea->prospecto->nombres ?? '') . ' ' . ($tarea->prospecto->apellidos ?? ''),
                    'telefono' => $tarea->prospecto->telefono ?? '',
                    'documento' => $tarea->prospecto->numero_documento ?? '',
                    'proyecto' => $tarea->prospecto->proyecto->nombre ?? '',
                    'fuente_referencia' => $tarea->prospecto->formaContacto->nombre ?? '',
                    'fecha_registro' => $tarea->prospecto->created_at ? $tarea->prospecto->created_at->format('d/m/Y') : '',
                    'fecha_ultimo_contacto' => $tarea->prospecto->fecha_ultimo_contacto ? $tarea->prospecto->fecha_ultimo_contacto->format('d/m/Y') : '',
                    'fecha_tarea' => $tarea->fecha_realizar ? $tarea->fecha_realizar->format('d/m/Y') : '',
                    'responsable' => $tarea->usuarioAsignado->name ?? '',
                ];
            });

        $pdf = Pdf::loadView('pdf.seguimientos', compact('seguimientos'));
        
        return $pdf->download('seguimientos_' . date('Y-m-d_H-i-s') . '.pdf');
    }
}