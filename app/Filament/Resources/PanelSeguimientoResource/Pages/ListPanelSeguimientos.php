<?php

namespace App\Filament\Resources\PanelSeguimientoResource\Pages;

use App\Filament\Resources\PanelSeguimientoResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

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
        $query = parent::getTableQuery()
            ->with(['prospecto.proyecto', 'prospecto.comoSeEntero', 'usuarioAsignado']);

        // Si no se ha seleccionado un tipo de gestión, no mostrar registros
        if (!$this->filtros['filtro_tipo_gestion_aplicado']) {
            return $query->whereRaw('1 = 0');
        }

        // Aplicar filtros solo si se han establecido valores
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

        if ($this->filtros['fecha_inicio']) {
            $query->whereDate('fecha_realizar', '>=', Carbon::parse($this->filtros['fecha_inicio']));
        }

        if ($this->filtros['fecha_fin']) {
            $query->whereDate('fecha_realizar', '<=', Carbon::parse($this->filtros['fecha_fin']));
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