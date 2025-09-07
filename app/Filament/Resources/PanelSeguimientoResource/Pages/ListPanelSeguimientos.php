<?php

namespace App\Filament\Resources\PanelSeguimientoResource\Pages;

use App\Filament\Resources\PanelSeguimientoResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

class ListPanelSeguimientos extends ListRecords
{
    protected static string $resource = PanelSeguimientoResource::class;

    protected $listeners = [
        'updateTableFilters' => 'updateFilters',
        'refreshTable' => '$refresh'
    ];

    public $filtros = [
        'proyecto_id' => null,
        'usuario_id' => null,
        'como_se_entero_id' => null,
        'tipo_gestion_id' => null,
        'fecha_inicio' => null,
        'fecha_fin' => null,
        'nivel_interes_id' => null,
        'rango_acciones' => null,
        'vencimiento' => null, 
        'filtro_tipo_gestion_aplicado' => false // Nueva bandera específica para tipo gestión
    ];

    public function updateFilters($filters)
    {
        $this->filtros = [
            'proyecto_id' => $filters['proyecto'] ?? null,
            'usuario_id' => ($filters['usuario_id'] ?? 0) != 0 ? $filters['usuario_id'] : null,
            'como_se_entero_id' => ($filters['comoSeEntero'] ?? 0) != 0 ? $filters['comoSeEntero'] : null,
            'tipo_gestion_id' => $filters['tipo_gestion_id'] ?? null,
            'fecha_inicio' => $filters['fechaInicio'] ?? null,
            'fecha_fin' => $filters['fechaFin'] ?? null,
            'nivel_interes_id' => ($filters['NivelInteres'] ?? 0) != 0 ? $filters['NivelInteres'] : null,
            'rango_acciones' => ($filters['rangoAcciones'] ?? 0) != 0 ? $filters['rangoAcciones'] : null,
            'vencimiento' => ($filters['vencimiento'] ?? 0) != 0 ? $filters['vencimiento'] : null,
            'filtro_tipo_gestion_aplicado' => !empty($filters['tipo_gestion_id']) // Solo true cuando se selecciona tipo gestión
        ];

        $this->resetPage();
    }

    protected function getTableQuery(): Builder
    {
        $latestTareas = DB::table('tareas as t1')
            ->selectRaw('MAX(t1.id) as id')
            ->join('prospectos as p', 'p.id', '=', 't1.prospecto_id')
            ->when($this->filtros['proyecto_id'], fn ($q) =>
                $q->where('p.proyecto_id', $this->filtros['proyecto_id'])
            )
            ->when($this->filtros['como_se_entero_id'], fn ($q) =>
                $q->where('p.como_se_entero_id', $this->filtros['como_se_entero_id'])
            )
            ->when($this->filtros['tipo_gestion_id'], fn ($q) =>
                $q->where('p.tipo_gestion_id', $this->filtros['tipo_gestion_id'])
            )
            ->when($this->filtros['fecha_inicio'], fn ($q) =>
                $q->whereDate('t1.fecha_realizar', '>=', Carbon::parse($this->filtros['fecha_inicio']))
            )
            ->when($this->filtros['fecha_fin'], fn ($q) =>
                $q->whereDate('t1.fecha_realizar', '<=', Carbon::parse($this->filtros['fecha_fin']))
            )
            ->when($this->filtros['nivel_interes_id'], fn ($q) =>
                $q->where('t1.nivel_interes_id', $this->filtros['nivel_interes_id'])
            )
            ->when($this->filtros['rango_acciones'], function ($q) {
                switch ($this->filtros['rango_acciones']) {
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

        $query = \App\Models\Tarea::query()
            ->with(['prospecto.proyecto', 'prospecto.comoSeEntero', 'usuarioAsignado'])
            ->whereIn('id', $latestTareas)
            ->when($this->filtros['usuario_id'], fn ($q) =>
                $q->where('usuario_asignado_id', $this->filtros['usuario_id'])
            )
            ->when($this->filtros['vencimiento'], function ($q) {
                switch ($this->filtros['vencimiento']) {
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

        if (!$this->filtros['filtro_tipo_gestion_aplicado']) {
            return $query->whereRaw('1 = 0');
        }

        return $query;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PanelSeguimientoResource\Widgets\SeguimientoFilters::class,
        ];
    }

    protected function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }
}