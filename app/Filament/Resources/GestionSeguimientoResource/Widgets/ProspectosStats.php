<?php

namespace App\Filament\Resources\GestionSeguimientoResource\Widgets;

use App\Models\Prospecto;
use App\Models\Proyecto;
use App\Models\User;
use App\Models\FormaContacto;
use App\Models\ComoSeEntero;
use App\Models\NivelInteres;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class ProspectosStats extends Widget implements HasForms{
    use InteractsWithForms;

    protected static string $view = 'filament.resources.gestion-seguimiento-resource.prospecto-stats-form';

        protected function getFormSchema(): array

    {
        return [
            Card::make()
                ->schema([
                    Grid::make(4)->schema([

                        Select::make('usuario_asignado')
                            ->label('Usuario Asignado')
                            ->options(User::all()->pluck('name', 'id'))
                            ->searchable()
                            ->default('all')
                            ->placeholder('Todos'),

                        Select::make('usuario_asignado')
                            ->label('Usuario Asignado')
                            ->options(User::all()->pluck('name', 'id'))
                            ->searchable()
                            ->default('all')
                            ->placeholder('Todos'),
                            
                        TextInput::make('nombres')
                            ->label('Nombres')
                            ->placeholder('Ingresar Nombres'),
                            
                        TextInput::make('numero_documento')
                            ->label('No Documento')
                            ->placeholder('Ingresar Nº documento'),
                            
                        Select::make('estado')
                            ->label('Estado')
                            ->options([
                                'ACTIVO' => 'Activo',
                                'INACTIVO' => 'Inactivo',
                                'POTENCIAL' => 'Potencial'
                            ])
                            ->default('ACTIVO'),
                    ]),
                    
                    Grid::make(4)->schema([
                        DatePicker::make('fecha_inicio')
                            ->label('Fecha Registro Inicio')
                            ->displayFormat('d/m/Y')
                            ->default(Carbon::now()->subMonths(3)),
                            
                        DatePicker::make('fecha_fin')
                            ->label('Fecha Registro Fin')
                            ->displayFormat('d/m/Y')
                            ->default(Carbon::now()),
                            
                        Select::make('tipo_fecha')
                            ->label('Tipo Fecha')
                            ->options([
                                'registro' => 'Registro',
                                'contacto' => 'Último Contacto',
                                'operacion' => 'Última Operación'
                            ])
                            ->default('registro'),
                            
                        Select::make('proyecto_id')
                            ->label('Proyecto')
                            ->options(Proyecto::all()->pluck('nombre', 'id'))
                            ->searchable()
                            ->placeholder('Todos'),
                    ]),
                    
                    Grid::make(4)->schema([
                        Select::make('nivel_interes')
                            ->label('Nivel de Interés')
                            ->options(NivelInteres::all()->pluck('nombre', 'id'))
                            ->searchable()
                            ->placeholder('Todos'),
                            
                        Select::make('forma_contacto')
                            ->label('Forma de Contacto')
                            ->options(FormaContacto::all()->pluck('nombre', 'id'))
                            ->searchable()
                            ->placeholder('Todos'),
                            
                        Select::make('como_se_entero_id')
                            ->label('Como se Enteró')
                            ->options(ComoSeEntero::all()->pluck('nombre', 'id'))
                            ->searchable()
                            ->placeholder('Todos'),
                            
                        Grid::make(2)->schema([
                            Checkbox::make('eventos_vencidos')
                                ->label('Eventos Vencidos'),
                                
                            Checkbox::make('con_score')
                                ->label('Con Score'),
                                
                            Checkbox::make('prospecto_nuevo')
                                ->label('Prospecto Nuevo'),
                                

])->columns(4)
                    ]),
                ]),
        ];
    }
   
}