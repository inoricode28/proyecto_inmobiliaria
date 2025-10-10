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
        // Si se pasó un Builder, úsalo; si se pasó una Collection, conviértelo a Builder mínimo.
        $query = $this->query instanceof \Illuminate\Database\Eloquent\Builder
            ? $this->query
            : (is_null($this->query) ? Tarea::query() : Tarea::query()->whereIn('id', collect($this->query)->pluck('id')));

        // Obtener las tareas con sus relaciones
        $tareas = $query->with(['prospecto.proyecto', 'prospecto.comoSeEntero', 'usuarioAsignado'])
            ->get();

        // Agrupar por prospecto para evitar duplicados
        $prospectosProcesados = [];
        $seguimientos = collect();

        foreach ($tareas as $tarea) {
            $prospectoId = $tarea->prospecto->id;
            
            // Si ya procesamos este prospecto, saltarlo
            if (in_array($prospectoId, $prospectosProcesados)) {
                continue;
            }
            
            $prospectosProcesados[] = $prospectoId;
            
            // Obtener la fecha de contacto usando el método que creamos
            $fechaContacto = \App\Models\Tarea::getFechaContactoProspecto($prospectoId);
            
            $seguimientos->push([
                'id' => $tarea->prospecto->id,
                'nombres' => ($tarea->prospecto->nombres ?? '') . ' ' . ($tarea->prospecto->ape_paterno ?? '') . ' ' . ($tarea->prospecto->ape_materno ?? ''),
                'telefono' => $tarea->prospecto->celular ?? $tarea->prospecto->telefono ?? '',
                'documento' => $tarea->prospecto->numero_documento ?? '',
                'proyecto' => $tarea->prospecto->proyecto->nombre ?? '',
                'fuente_referencia' => $tarea->prospecto->comoSeEntero->nombre ?? '',
                'fecha_registro' => $tarea->prospecto->created_at ? $tarea->prospecto->created_at->format('d/m/Y') : '',
                'fecha_ultimo_contacto' => $fechaContacto ? $fechaContacto->format('d/m/Y H:i') : '',
                'fecha_tarea' => $tarea->fecha_realizar ? $tarea->fecha_realizar->format('d/m/Y') : '',
                'responsable' => $tarea->usuarioAsignado->name ?? '',
            ]);
        }

        $pdf = Pdf::loadView('pdf.seguimientos', compact('seguimientos'));
        return $pdf->download('seguimientos_' . date('Y-m-d_H-i-s') . '.pdf');
    }
}
