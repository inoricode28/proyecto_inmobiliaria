<?php

namespace App\Filament\Resources\GestionSeguimientoResource\Pages;

use App\Filament\Resources\GestionSeguimientoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ListGestionSeguimientos extends ListRecords
{
    protected static string $resource = GestionSeguimientoResource::class;

    public $filters = [];

    protected function getListeners(): array
    {
        return [
            'aplicarFiltros' => 'aplicarFiltros',
        ];
    }

    public function aplicarFiltros($filtros)
    {
        $this->filters = $filtros;
        $this->resetPage();
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Prospecto')
                ->button()
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            GestionSeguimientoResource\Widgets\ProspectosStats::class,
        ];
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No hay prospectos registrados';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Haz clic en "Nuevo Prospecto" para agregar uno nuevo';
    }

    protected function getTableEmptyStateIcon(): string
    {
        return 'heroicon-o-user-group';
    }

    protected function getTableQuery(): Builder
{
    $query = parent::getTableQuery();
    
    if (isset($this->filters)) {
        $query->when($this->filters['proyecto_id'] ?? null, fn($q, $proyecto_id) => $q->where('proyecto_id', $proyecto_id))
            ->when($this->filters['usuario_asignado'] ?? null, function($q, $usuario_id) {
                return $q->whereHas('tareas', function($q) use ($usuario_id) {
                    $q->where('usuario_asignado_id', $usuario_id);
                });
            })
            ->when($this->filters['nombres'] ?? null, fn($q, $nombres) => $q->where('nombres', 'like', "%$nombres%"))
            ->when($this->filters['numero_documento'] ?? null, fn($q, $doc) => $q->where('numero_documento', $doc))
            // Filtro por nivel de interés desde la tabla tareas
            ->when($this->filters['nivel_interes'] ?? null, function($q, $nivel) {
                return $q->whereHas('tareaAsignada', function($q) use ($nivel) {
                    $q->where('nivel_interes_id', $nivel);
                });
            })
            ->when($this->filters['forma_contacto'] ?? null, fn($q, $forma) => $q->where('forma_contacto_id', $forma))
            ->when($this->filters['como_se_entero_id'] ?? null, fn($q, $como) => $q->where('como_se_entero_id', $como))
            ->when($this->filters['eventos_vencidos'] ?? false, fn($q) => $q->whereHas('eventos', fn($q) => $q->where('fecha', '<', now())))
            ->when($this->filters['con_score'] ?? false, fn($q) => $q->whereNotNull('score'))
            ->when($this->filters['prospecto_nuevo'] ?? false, fn($q) => $q->where('created_at', '>', now()->subDays(7)));

        // Filtro de fechas
        if (isset($this->filters['fecha_inicio'])) {
            $field = match($this->filters['tipo_fecha'] ?? 'registro') {
                'contacto' => 'ultimo_contacto',
                'operacion' => 'ultima_operacion',
                default => 'created_at'
            };
            $query->whereDate($field, '>=', Carbon::parse($this->filters['fecha_inicio']));
        }

        if (isset($this->filters['fecha_fin'])) {
            $field = match($this->filters['tipo_fecha'] ?? 'registro') {
                'contacto' => 'ultimo_contacto',
                'operacion' => 'ultima_operacion',
                default => 'created_at'
            };
            $query->whereDate($field, '<=', Carbon::parse($this->filters['fecha_fin']));
        }
    }

    return $query;

    }
}