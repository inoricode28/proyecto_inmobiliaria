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
                                ->options([
                                    ...Proyecto::query()
                                        ->orderBy('nombre')
                                        ->pluck('nombre', 'id')
                                        ->toArray()
                                        
                                ])
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

                    Grid::make(2)
                        ->schema([
                            DatePicker::make('fechaInicio')
                                ->label('Fecha Inicio')
                                ->displayFormat('d/m/Y')
                                ->reactive(),

                            DatePicker::make('fechaFin')
                                ->label('Fecha Fin')
                                ->displayFormat('d/m/Y')
                                ->reactive(),
                        ]),

                    Select::make('tipo_gestion_id')
                        ->label('Tipo de Gestión')
                        ->view('filament.resources.panel-seguimiento-resource.tipo-gestion-select') // opcional
                        ->reactive()
                        ->options(function (callable $get) {

        $filters = [
            'proyecto'     => $get('proyecto'),
            'usuario'      => $get('usuario'),
            'comoSeEntero' => $get('comoSeEntero'),
            'fechaInicio'  => $get('fechaInicio'),
            'fechaFin'     => $get('fechaFin'),
        ];

        $prospectosQuery = \App\Models\Prospecto::query();

        if ($filters['proyecto'] && $filters['proyecto'] !== 'TODOS') {
            $prospectosQuery->where('proyecto_id', $filters['proyecto']);
        }

        if ($filters['comoSeEntero'] && $filters['comoSeEntero'] !== 'TODOS') {
            $prospectosQuery->where('como_se_entero_id', $filters['comoSeEntero']);
        }

        if ($filters['usuario'] && $filters['usuario'] !== 'TODOS') {
            $prospectosQuery->whereHas('tareas', function ($q) use ($filters) {
                $q->where('usuario_asignado_id', $filters['usuario']);
            });
        }

        if ($filters['fechaInicio']) {
            $prospectosQuery->whereDate('fecha_registro', '>=', $filters['fechaInicio']);
        }
        if ($filters['fechaFin']) {
            $prospectosQuery->whereDate('fecha_registro', '<=', $filters['fechaFin']);
        }

        $conteos = $prospectosQuery
            ->whereNotNull('tipo_gestion_id')
            ->selectRaw('tipo_gestion_id, COUNT(*) as total')
            ->groupBy('tipo_gestion_id')
            ->pluck('total', 'tipo_gestion_id');

        return \App\Models\TipoGestion::orderBy('nombre')
            ->get()
            ->mapWithKeys(function ($tipo) use ($conteos) {
                $total = $conteos->get($tipo->id, 0);
                return [$tipo->id => "{$tipo->nombre} ({$total})"];
            });
    })
    ->afterStateUpdated(fn () => $this->submitFilters())



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