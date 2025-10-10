<?php

namespace App\Exports;

use App\Models\Tarea;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SeguimientosExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $query;

    public function __construct($query = null)
    {
        $this->query = $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombres',
            'Teléfono',
            'N° Documento',
            'Proyecto',
            'Fuente de Referencia',
            'Fecha de Registro',
            'Fecha Último Contacto',
            'Fecha de Tarea',
            'Responsable',
        ];
    }

    public function collection()
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
        $resultado = collect();

        foreach ($tareas as $tarea) {
            $prospectoId = $tarea->prospecto->id;

            // Si ya procesamos este prospecto, saltarlo
            if (in_array($prospectoId, $prospectosProcesados)) {
                continue;
            }

            $prospectosProcesados[] = $prospectoId;

            // Obtener la fecha de contacto usando el método que creamos
            $fechaContacto = \App\Models\Tarea::getFechaContactoProspecto($prospectoId);

            $resultado->push([
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

        return $resultado;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    /**
     * Obtiene la fecha de la última tarea efectiva del prospecto
     * Una tarea se considera efectiva si el prospecto está en estado "Contactados" (tipo_gestion_id = 3)
     */
    private function obtenerFechaUltimaTareaEfectiva($prospecto)
    {
        // Si el prospecto está en estado "Contactados", buscar la última tarea
        if ($prospecto->tipo_gestion_id == 3) {
            $ultimaTarea = \App\Models\Tarea::where('prospecto_id', $prospecto->id)
                ->orderBy('fecha_realizar', 'desc')
                ->first();

            return $ultimaTarea && $ultimaTarea->fecha_realizar
                ? $ultimaTarea->fecha_realizar->format('d/m/Y')
                : '';
        }

        return '';
    }
}