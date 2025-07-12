<?php

namespace App\Filament\Resources\PanelSeguimientoResource\Widgets;

use App\Models\Proyecto;
use App\Models\User;
use App\Models\ComoSeEntero;
use App\Models\TipoGestion;
use App\Models\Tarea;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\DatePicker;
use Filament\Widgets\Widget;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Carbon;

class SeguimientoFilters extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.resources.panel-seguimiento-resource.seguimiento-filters';

    public array $data = [];

    protected function getFormSchema(): array
    {
        return [
            Card::make()
                ->schema([
                    Grid::make(3)
                        ->schema([
                            Select::make('proyecto')
                                ->label('Proyecto')
                                ->options(Proyecto::orderBy('nombre')->pluck('nombre', 'id'))
                                ->placeholder('TODOS')
                                ->reactive()
                                ->afterStateUpdated(fn () => $this->submitFilters()),

                            Select::make('usuario_id')
                                ->label('Usuario')
                                ->options(
                                    collect([0 => 'TODOS'])
                                        ->merge(User::orderBy('name')->pluck('name', 'id'))
                                )
                                ->default(0)
                                ->reactive()
                                ->afterStateUpdated(fn () => $this->submitFilters()),

                            Select::make('comoSeEntero')
                                ->label('Cómo se enteró')
                                ->options(
                                    collect([0 => 'TODOS'])
                                        ->merge(ComoSeEntero::orderBy('nombre')->pluck('nombre', 'id'))
                                )
                                ->default(0)
                                ->reactive()
                                ->afterStateUpdated(fn () => $this->submitFilters()),
                        ]),

                    Grid::make(2)
                        ->schema([
                            DatePicker::make('fechaInicio')
                                ->label('Fecha Inicio')
                                ->displayFormat('d/m/Y')
                                ->reactive()
                                ->afterStateUpdated(fn () => $this->submitFilters()),

                            DatePicker::make('fechaFin')
                                ->label('Fecha Fin')
                                ->displayFormat('d/m/Y')
                                ->reactive()
                                ->afterStateUpdated(fn () => $this->submitFilters()),
                        ]),

                    Select::make('tipo_gestion_id')
                        ->label('Tipo de Gestión')
                        ->view('filament.resources.panel-seguimiento-resource.tipo-gestion-select')
                        ->reactive()
                        ->afterStateUpdated(fn () => $this->submitFilters())
                ])
        ];
    }

    public function getConteosParaVista(array $filters = [])
    {
        $query = Tarea::query()
            ->join('prospectos', 'prospectos.id', '=', 'tareas.prospecto_id')
            ->whereNotNull('prospectos.tipo_gestion_id');

        // Aplicar filtros solo si están definidos y no son TODOS (0)
        if (!empty($filters['proyecto'])) {
            $query->where('prospectos.proyecto_id', $filters['proyecto']);
        }

        if (!empty($filters['usuario_id']) && $filters['usuario_id'] != 0) {
            $query->where('tareas.usuario_asignado_id', $filters['usuario_id']);
        }

        if (!empty($filters['comoSeEntero']) && $filters['comoSeEntero'] != 0) {
            $query->where('prospectos.como_se_entero_id', $filters['comoSeEntero']);
        }

        if (!empty($filters['fechaInicio'])) {
            $query->whereDate('tareas.fecha_realizar', '>=', Carbon::parse($filters['fechaInicio']));
        }

        if (!empty($filters['fechaFin'])) {
            $query->whereDate('tareas.fecha_realizar', '<=', Carbon::parse($filters['fechaFin']));
        }

        $conteos = $query->clone()
            ->select('prospectos.tipo_gestion_id')
            ->selectRaw('COUNT(DISTINCT tareas.id) as total')
            ->groupBy('prospectos.tipo_gestion_id')
            ->pluck('total', 'tipo_gestion_id');

        return TipoGestion::orderBy('nombre')
            ->get()
            ->mapWithKeys(function ($tipo) use ($conteos) {
                return [$tipo->id => $conteos->get($tipo->id, 0)];
            });
    }

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    public function submitFilters()
    {
        $this->emit('updateTableFilters', $this->form->getState());
    }
}
