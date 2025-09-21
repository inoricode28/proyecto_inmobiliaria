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

    public function collection(): Collection
    {
        $query = $this->query ?: Tarea::query();
        
        return $query->with(['prospecto.proyecto', 'prospecto.formaContacto', 'usuarioAsignado'])
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
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}