<?php

namespace App\Filament\Resources\PanelSeguimientoResource\Widgets;

use App\Models\Proyecto;
use App\Models\User;
use App\Models\ComoSeEntero;
use App\Models\TipoGestion;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\DatePicker;
use Filament\Widgets\Widget;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class SeguimientoFilters extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.resources.panel-seguimiento-resource.seguimiento-filters';

    public ?array $data = [];


    protected $proyecto = 'TODOS';
    protected $usuario = 'TODOS';
    protected $comoSeEntero = 'TODOS';
    protected $tipo_gestion_id;

    protected function getFormSchema(): array
    {
        return [
            Card::make()
                ->schema([
                    Grid::make(3)
                        ->schema([
                            Select::make('proyecto')
                                ->label('Proyecto')
                                ->options([
                                    'TODOS' => 'TODOS',
                                    ...Proyecto::query()
                                        ->orderBy('nombre')
                                        ->pluck('nombre', 'id')
                                        ->toArray()
                                ])
                                ->default('TODOS')
                                ->reactive(),

                            Select::make('usuario')
                                ->label('Usuario')
                                ->options([
                                    'TODOS' => 'TODOS',
                                    ...User::query()
                                        ->orderBy('name')
                                        ->pluck('name', 'id')
                                        ->toArray()
                                ])
                                ->default('TODOS')
                                ->reactive(),

                            Select::make('comoSeEntero')
                                ->label('Cómo se enteró')

                                ->options([
                                    'TODOS' => 'TODOS',
                                    ...ComoSeEntero::query()
                                        ->orderBy('nombre')
                                        ->pluck('nombre', 'id')
                                        ->toArray()
                                ])
                                ->default('TODOS')
                                ->reactive(),

                        ]),

                    Grid::make(3)
                        ->schema([

                            DatePicker::make('fechaInicio')
                                ->label('Fecha Inicio')
                                ->displayFormat('d/m/Y'),

                            DatePicker::make('fechaFin')
                                ->label('Fecha Fin')
                                ->displayFormat('d/m/Y'),

                        ]),

                    Select::make('tipo_gestion_id')
    ->label('Tipo de Gestión')
    ->view('filament.resources.panel-seguimiento-resource.tipo-gestion-select')
    ->reactive()
    ->options(function (callable $get) {
        $filters = [
            'proyecto' => $get('proyecto'),
            'usuario' => $get('usuario'),
            'comoSeEntero' => $get('comoSeEntero'),
        ];

        $query = \App\Models\TipoGestion::query()
            ->withCount(['prospectos as prospectos_count' => function ($q) use ($filters) {
                // Proyecto
                if (!empty($filters['proyecto']) && $filters['proyecto'] !== 'TODOS') {
                    $q->where('proyecto_id', $filters['proyecto']);
                }

                // Cómo se enteró
                if (!empty($filters['comoSeEntero']) && $filters['comoSeEntero'] !== 'TODOS') {
                    $q->where('como_se_entero_id', $filters['comoSeEntero']);
                }

                // Usuario (desde tareas del prospecto)
                if (!empty($filters['usuario']) && $filters['usuario'] !== 'TODOS') {
                    $q->whereHas('tareas', function ($tareaQuery) use ($filters) {
                        $tareaQuery->where('usuario_asignado_id', $filters['usuario']);
                    });
                }
            }])
            ->orderBy('nombre');

        return $query->get()->mapWithKeys(function ($tipo) {
            return [$tipo->id => "{$tipo->nombre} ({$tipo->prospectos_count})"];
        });
    })
    ->afterStateUpdated(fn () => $this->submitFilters()),




                ])
        ];
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
