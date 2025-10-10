<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use App\Exports\SeguimientosExport;
use App\Exports\SeguimientosPdfExport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class SeguimientoExportController extends Controller
{
    public function exportExcel(Request $request)
    {
        $query = $this->buildTableQuery($request);
        // Pasar el query builder directamente para evitar llamar with() sobre una Collection
        return Excel::download(new SeguimientosExport($query), 'seguimientos.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = $this->buildTableQuery($request);
        // Usar la clase de exportación para asegurar el mismo agrupamiento y filtrado que Excel
        return (new SeguimientosPdfExport($query))->export();
    }

    private function buildTableQuery(Request $request)
    {
        // Replicar exactamente la lógica de ListPanelSeguimientos::getTableQuery()
        $filtros = [
            // Aceptar nombres alternos desde el Widget (proyecto, comoSeEntero, fechaInicio, etc.)
            'proyecto_id' => $request->get('proyecto_id') ?? $request->get('proyecto'),
            'usuario_id' => $request->get('usuario_id'),
            'como_se_entero_id' => $request->get('como_se_entero_id') ?? $request->get('comoSeEntero'),
            'tipo_gestion_id' => $request->get('tipo_gestion_id'),
            'fecha_inicio' => $request->get('fecha_inicio') ?? $request->get('fechaInicio'),
            'fecha_fin' => $request->get('fecha_fin') ?? $request->get('fechaFin'),
            'nivel_interes_id' => $request->get('nivel_interes_id') ?? $request->get('NivelInteres'),
            'rango_acciones' => $request->get('rango_acciones') ?? $request->get('rangoAcciones'),
            'vencimiento' => $request->get('vencimiento'),
            // Bandera igual que en ListPanel: solo true cuando se selecciona tipo gestión
            'filtro_tipo_gestion_aplicado' => !empty($request->get('tipo_gestion_id'))
        ];

        $latestTareas = DB::table('tareas as t1')
            ->selectRaw('MAX(t1.id) as id')
            ->join('prospectos as p', 'p.id', '=', 't1.prospecto_id')
            ->when($filtros['proyecto_id'], fn ($q) =>
                $q->where('p.proyecto_id', $filtros['proyecto_id'])
            )
            ->when($filtros['como_se_entero_id'], fn ($q) =>
                $q->where('p.como_se_entero_id', $filtros['como_se_entero_id'])
            )
            ->when($filtros['tipo_gestion_id'], fn ($q) =>
                $q->where('p.tipo_gestion_id', $filtros['tipo_gestion_id'])
            )
            ->when($filtros['fecha_inicio'], fn ($q) =>
                $q->whereDate('t1.fecha_realizar', '>=', Carbon::parse($filtros['fecha_inicio']))
            )
            ->when($filtros['fecha_fin'], fn ($q) =>
                $q->whereDate('t1.fecha_realizar', '<=', Carbon::parse($filtros['fecha_fin']))
            )
            ->when($filtros['nivel_interes_id'], fn ($q) =>
                $q->where('t1.nivel_interes_id', $filtros['nivel_interes_id'])
            )
            ->when($filtros['rango_acciones'], function ($q) use ($filtros) {
                switch ($filtros['rango_acciones']) {
                    case '1':
                        $q->whereRaw('(SELECT COUNT(*) FROM tareas t2
                                    WHERE t2.prospecto_id = p.id
                                    AND t2.deleted_at IS NULL) = 1');
                        break;
                    case '2-5':
                        $q->whereRaw('(SELECT COUNT(*) FROM tareas t2
                                    WHERE t2.prospecto_id = p.id
                                    AND t2.deleted_at IS NULL) BETWEEN 2 AND 5');
                        break;
                    case '6-10':
                        $q->whereRaw('(SELECT COUNT(*) FROM tareas t2
                                    WHERE t2.prospecto_id = p.id
                                    AND t2.deleted_at IS NULL) BETWEEN 6 AND 10');
                        break;
                    case '11+':
                        $q->whereRaw('(SELECT COUNT(*) FROM tareas t2
                                    WHERE t2.prospecto_id = p.id
                                    AND t2.deleted_at IS NULL) >= 11');
                        break;
                }
            })
            ->whereNull('t1.deleted_at')
            ->groupBy('t1.prospecto_id');

        $query = Tarea::query()
            ->with(['prospecto.proyecto', 'prospecto.comoSeEntero', 'usuarioAsignado'])
            ->whereIn('id', $latestTareas)
            ->when($filtros['usuario_id'], fn ($q) =>
                $q->where('usuario_asignado_id', $filtros['usuario_id'])
            )
            ->when($filtros['vencimiento'], function ($q) use ($filtros) {
                switch ($filtros['vencimiento']) {
                    case 'vencido_1':
                        $q->whereRaw('DATEDIFF(CURDATE(), fecha_realizar) = 1');
                        break;
                    case 'vencido_2':
                        $q->whereRaw('DATEDIFF(CURDATE(), fecha_realizar) = 2');
                        break;
                    case 'vencido_3+':
                        $q->whereRaw('DATEDIFF(CURDATE(), fecha_realizar) >= 3');
                        break;
                    case 'hoy':
                        $q->whereDate('fecha_realizar', '=', now()->toDateString());
                        break;
                    case 'por_vencer':
                        $q->whereDate('fecha_realizar', '>', now()->toDateString());
                        break;
                }
            });

        // Si en el listado no aplica tipo de gestión, no muestra nada
        if (!$filtros['filtro_tipo_gestion_aplicado']) {
            return $query->whereRaw('1 = 0');
        }

        return $query;
    }

    private function formatNombres($prospecto)
    {
        if (!$prospecto) {
            return '-';
        }
        $tieneNombre = $prospecto->nombres && $prospecto->ape_paterno;
        return $tieneNombre
            ? $prospecto->nombres . ' ' . $prospecto->ape_paterno . ' ' . ($prospecto->ape_materno ?? '')
            : ($prospecto->razon_social ?? '-');
    }

    private function getFechaUltimoContacto($prospectoId)
    {
        $fechaContacto = \App\Models\Tarea::getFechaContactoProspecto($prospectoId);
        return $fechaContacto ? $fechaContacto->format('d/m/Y H:i') : '-';
    }
}
