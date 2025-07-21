<?php

namespace App\Filament\Resources\PanelSeguimientoResource\Pages;

use App\Filament\Resources\PanelSeguimientoResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Database\Query\Builder as QueryBuilder;

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
            'filtro_tipo_gestion_aplicado' => !empty($filters['tipo_gestion_id']) // Solo true cuando se selecciona tipo gestión
        ];

        $this->resetPage();
    }

    protected function getTableQuery(): Builder
    {
        $latestTareas = \DB::table('tareas as t1')
            ->selectRaw('MAX(t1.id) as id')
            ->join('prospectos as p', 'p.id', '=', 't1.prospecto_id')
            ->when($this->filtros['proyecto_id'], function (QueryBuilder $q) {
                $q->where('p.proyecto_id', $this->filtros['proyecto_id']);
            })
            ->when($this->filtros['como_se_entero_id'], function (QueryBuilder $q) {
                $q->where('p.como_se_entero_id', $this->filtros['como_se_entero_id']);
            })
            ->when($this->filtros['tipo_gestion_id'], function (QueryBuilder $q) {
                $q->where('p.tipo_gestion_id', $this->filtros['tipo_gestion_id']);
            })
            ->when($this->filtros['fecha_inicio'], function (QueryBuilder $q) {
                $q->whereDate('t1.fecha_realizar', '>=', Carbon::parse($this->filtros['fecha_inicio']));
            })
            ->when($this->filtros['fecha_fin'], function (QueryBuilder $q) {
                $q->whereDate('t1.fecha_realizar', '<=', Carbon::parse($this->filtros['fecha_fin']));
            })
            ->whereNull('t1.deleted_at')
            ->groupBy('t1.prospecto_id');

        $query = \App\Models\Tarea::query()
            ->with(['prospecto.proyecto', 'prospecto.comoSeEntero', 'usuarioAsignado'])
            ->whereIn('id', $latestTareas)
            ->when($this->filtros['usuario_id'], function ($q) {
                $q->where('usuario_asignado_id', $this->filtros['usuario_id']);
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