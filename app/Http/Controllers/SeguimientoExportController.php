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

class SeguimientoExportController extends Controller
{
    public function exportExcel(Request $request)
    {
        // Debug: Log todos los parámetros recibidos
        Log::info('Parámetros de exportación Excel:', $request->all());
        
        $query = $this->buildFilteredQuery($request);
        
        // Solo aplicar paginación si se reciben parámetros específicos de paginación
        $page = $request->get('page');
        $perPage = $request->get('per_page');
        
        Log::info('Paginación recibida:', ['page' => $page, 'per_page' => $perPage]);
        
        // Solo paginar si ambos parámetros están presentes y son válidos
        if ($page && $perPage && $page > 0 && $perPage > 0) {
            $query = $query->skip(($page - 1) * $perPage)->take($perPage);
            Log::info('Aplicando paginación:', ['page' => $page, 'per_page' => $perPage, 'skip' => ($page - 1) * $perPage, 'take' => $perPage]);
        } else {
            Log::info('No se aplicará paginación - exportando todos los registros filtrados');
        }
        
        // Debug: Contar registros antes de exportar
        $totalRecords = $query->count();
        Log::info('Total de registros a exportar:', ['count' => $totalRecords]);
        
        return Excel::download(new SeguimientosExport($query), 'seguimientos_' . date('Y-m-d_H-i-s') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        // Debug: Log todos los parámetros recibidos
        Log::info('Parámetros de exportación PDF:', $request->all());
        
        $query = $this->buildFilteredQuery($request);
        
        // Aplicar paginación si se especifica
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 10); // Filament usa 10 por defecto
        
        Log::info('Paginación aplicada PDF:', ['page' => $page, 'per_page' => $perPage]);
        
        if ($page && $perPage) {
            $query = $query->skip(($page - 1) * $perPage)->take($perPage);
        }
        
        // Debug: Contar registros antes de exportar
        $totalRecords = $query->count();
        Log::info('Total de registros PDF a exportar:', ['count' => $totalRecords]);
        
        return (new SeguimientosPdfExport($query))->export();
    }

    private function buildFilteredQuery(Request $request)
    {
        // Obtener filtros del request - exactamente como en la tabla
        $filtros = [
            'proyecto_id' => $request->get('proyecto'),
            'usuario_id' => ($request->get('usuario_id', 0) != 0) ? $request->get('usuario_id') : null,
            'como_se_entero_id' => ($request->get('comoSeEntero', 0) != 0) ? $request->get('comoSeEntero') : null,
            'tipo_gestion_id' => $request->get('tipo_gestion_id'),
            'fecha_inicio' => $request->get('fechaInicio'),
            'fecha_fin' => $request->get('fechaFin'),
            'nivel_interes_id' => ($request->get('NivelInteres', 0) != 0) ? $request->get('NivelInteres') : null,
            'rango_acciones' => ($request->get('rangoAcciones', 0) != 0) ? $request->get('rangoAcciones') : null,
            'vencimiento' => ($request->get('vencimiento', 0) != 0) ? $request->get('vencimiento') : null,
            'filtro_tipo_gestion_aplicado' => !empty($request->get('tipo_gestion_id'))
        ];

        // REPLICAR EXACTAMENTE LA LÓGICA DE LA TABLA
        // Paso 1: Construir la subconsulta de las últimas tareas (igual que en la tabla)
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

        // Paso 2: Construir la consulta principal (igual que en la tabla)
        $query = \App\Models\Tarea::query()
            ->with(['prospecto.proyecto', 'prospecto.comoSeEntero', 'prospecto.tipoGestion', 'usuarioAsignado', 'nivelInteres'])
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

        // Paso 3: CAMBIAR LA LÓGICA - NO DEVOLVER CONSULTA VACÍA
        // La tabla muestra datos incluso sin filtro de tipo gestión, así que las exportaciones también deben hacerlo
        return $query;
    }
}