<?php

namespace App\Filament\Resources\PanelSeguimientoResource\Pages;

use App\Filament\Resources\PanelSeguimientoResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

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
        'fecha_fin' => null
    ];

    public function updateFilters($filters)
    {
        $this->filtros = array_merge($this->filtros, $filters);
        $this->resetPage();
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        if ($this->filtros['proyecto_id']) {
            $query->whereHas('prospecto', function ($q) {
                $q->where('proyecto_id', $this->filtros['proyecto_id']);
            });
        }

        if ($this->filtros['usuario_id']) {
            $query->where('usuario_asignado_id', $this->filtros['usuario_id']);
        }

        if ($this->filtros['como_se_entero_id']) {
            $query->whereHas('prospecto', function ($q) {
                $q->where('como_se_entero_id', $this->filtros['como_se_entero_id']);
            });
        }

        if ($this->filtros['tipo_gestion_id']) {
            $query->whereHas('prospecto', function ($q) {
                $q->where('tipo_gestion_id', $this->filtros['tipo_gestion_id']);
            });
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