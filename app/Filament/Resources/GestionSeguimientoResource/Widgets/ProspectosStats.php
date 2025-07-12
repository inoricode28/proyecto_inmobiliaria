<?php

namespace App\Filament\Resources\GestionSeguimientoResource\Widgets;

use App\Models\Prospecto;
use App\Models\Proyecto;
use App\Models\User;
use App\Models\FormaContacto;
use App\Models\ComoSeEntero;
use App\Models\NivelInteres;
use Filament\Forms;
use Filament\Forms\Components\ViewField;
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

class ProspectosStats extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.resources.gestion-seguimiento-resource.prospecto-stats-form';

    // Propiedades para los filtros
    public $proyecto_id;
    public $usuario_asignado;
    public $nombres;
    public $numero_documento;
    public $estado = 'ACTIVO';
    public $fecha_inicio;
    public $fecha_fin;
    public $tipo_fecha = 'registro';
    public $nivel_interes;
    public $forma_contacto;
    public $como_se_entero_id;
    public $eventos_vencidos;
    public $con_score;
    public $prospecto_nuevo;

    protected function getFormSchema(): array
    {
        return [
            Card::make()
                ->schema([
                    Grid::make(4)->schema([
                        Select::make('proyecto_id')
                            ->label('Listar')
                            ->options(Proyecto::all()->pluck('nombre', 'id'))
                            ->searchable()
                            ->reactive()
                            ->placeholder('Todos'),

                        Select::make('usuario_asignado')
                            ->label('Usuario Asignado')
                            ->options(User::all()->pluck('name', 'id'))
                            ->searchable()
                            ->reactive()
                            ->placeholder('Todos'),

                        TextInput::make('nombres')
                            ->label('Nombres')
                            ->reactive()
                            ->placeholder('Ingresar Nombres'),

                        TextInput::make('numero_documento')
                            ->label('No Documento')
                            ->reactive()
                            ->placeholder('Ingresar Nº documento'),
                    ]),

                    Grid::make(4)->schema([
                        Select::make('estado')
                            ->label('Estado')
                            ->options([
                                'ACTIVO' => 'Activo',
                                'INACTIVO' => 'Inactivo',
                                'POTENCIAL' => 'Potencial'
                            ])
                            ->reactive()
                            ->default('ACTIVO'),

                        DatePicker::make('fecha_inicio')
                            ->label('Fecha Registro Inicio')
                            ->displayFormat('d/m/Y')
                            ->reactive()
                            ->default(Carbon::now()->subMonths(3)),

                        DatePicker::make('fecha_fin')
                            ->label('Fecha Registro Fin')
                            ->displayFormat('d/m/Y')
                            ->reactive()
                            ->default(Carbon::now()),

                        Select::make('tipo_fecha')
                            ->label('Tipo Fecha')
                            ->options([
                                'registro' => 'Registro',
                                'contacto' => 'Último Contacto',
                                'operacion' => 'Última Operación'
                            ])
                            ->reactive()
                            ->default('registro'),
                    ]),

                    Grid::make(4)->schema([
                        Select::make('proyecto_id')
                            ->label('Proyecto')
                            ->options(Proyecto::all()->pluck('nombre', 'id'))
                            ->searchable()
                            ->reactive()
                            ->placeholder('Todos'),

                        Select::make('nivel_interes')
                            ->label('Nivel de Interés')
                            ->options(NivelInteres::all()->pluck('nombre', 'id'))
                            ->searchable()
                            ->reactive()
                            ->placeholder('Todos'),

                        Select::make('forma_contacto')
                            ->label('Forma de Contacto')
                            ->options(FormaContacto::all()->pluck('nombre', 'id'))
                            ->searchable()
                            ->reactive()
                            ->placeholder('Todos'),

                        Select::make('como_se_entero_id')
                            ->label('Como se Enteró')
                            ->options(ComoSeEntero::all()->pluck('nombre', 'id'))
                            ->searchable()
                            ->reactive()
                            ->placeholder('Todos'),
                    ]),

                    Grid::make(4)->schema([
                        Checkbox::make('eventos_vencidos')
                            ->label('Eventos Vencidos')
                            ->reactive(),

                        Checkbox::make('con_score')
                            ->label('Con Score')
                            ->reactive(),

                        Checkbox::make('prospecto_nuevo')
                            ->label('Prospecto Nuevo')
                            ->reactive(),

                        ViewField::make('buscar_button')
                            ->label('')
                            ->view('filament.resources.gestion-seguimiento-resource.buscar-button')
                    ])->columns(4)
                ]),
        ];
    }

    public function buscar()
    {
        $this->emit('aplicarFiltros', [
            'proyecto_id' => $this->proyecto_id,
            'usuario_asignado' => $this->usuario_asignado,
            'nombres' => $this->nombres,
            'numero_documento' => $this->numero_documento,
            'estado' => $this->estado,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'tipo_fecha' => $this->tipo_fecha,
            'nivel_interes' => $this->nivel_interes,
            'forma_contacto' => $this->forma_contacto,
            'como_se_entero_id' => $this->como_se_entero_id,
            'eventos_vencidos' => $this->eventos_vencidos,
            'con_score' => $this->con_score,
            'prospecto_nuevo' => $this->prospecto_nuevo,
        ]);
    }
}