<?php

namespace App\Filament\Resources;

use App\Models\Tarea;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use App\Models\FormaContacto;
use App\Models\NivelInteres;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Grid;
use Filament\Notifications\Notification;
use App\Filament\Resources\PanelSeguimientoResource\Pages;
use App\Filament\Resources\PanelSeguimientoResource\Widgets\SeguimientoFilters;
use App\Filament\Resources\Proforma\ProformaResource;
use App\Filament\Resources\Separaciones\SeparacionResource;
class PanelSeguimientoResource extends Resource
{
    protected static ?string $model = Tarea::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-check';
    protected static ?string $navigationLabel = 'Panel de Seguimientos';
    protected static ?string $modelLabel = 'Seguimiento';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Gestión Seguimiento';
    protected static ?string $routePath = 'seguimiento';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('prospecto.nombres')
                    ->label('Nombres')
                    ->formatStateUsing(function ($record) {
                        $prospecto = $record->prospecto;
                        if (!$prospecto) {
                            return '-';
                        }
                        $tieneNombre = $prospecto->nombres && $prospecto->ape_paterno;
                        return $tieneNombre
                            ? $prospecto->nombres . ' ' . $prospecto->ape_paterno . ' ' . ($prospecto->ape_materno ?? '')
                            : ($prospecto->razon_social ?? '-');
                    })
                    ->searchable(),

                TextColumn::make('prospecto.celular')->label('Teléfono')->searchable(),
                TextColumn::make('prospecto.proyecto.nombre')->label('Proyecto'),
                TextColumn::make('prospecto.comoSeEntero.nombre')->label('Cómo se enteró'),
                TextColumn::make('prospecto.fecha_registro')->label('Fec. Registro')->date('d/m/Y'),
                TextColumn::make('fecha_contacto')->label('Fec. Últ. Contacto')->dateTime('d/m/Y H:i'),
                TextColumn::make('fecha_realizar')->label('Fec. Tarea')->dateTime('d/m/Y'),
                TextColumn::make('usuarioAsignado.name')->label('Responsable'),
            ])
            ->actions([
                // Botón Realizar Tarea
                Action::make('realizarTarea')
                    ->label('Realizar Tarea')
                    ->icon('heroicon-o-clipboard-check')
                    ->color('primary')
                    ->button()
                    ->size('sm')
                    ->visible(fn ($record) =>in_array($record->prospecto?->tipo_gestion_id, [1, 2, 3]))
                    ->extraAttributes([
                        'class' => 'mr-2',
                        'style' => 'background-color: #1d4ed8; border-color: #1e40af; color: white;',
                    ])
                    ->modalHeading(function ($record) {
                        $nombreCompleto = $record->prospecto->nombres
                            ? $record->prospecto->nombres . ' ' . $record->prospecto->ape_paterno . ($record->prospecto->ape_materno ? ' ' . $record->prospecto->ape_materno : '')
                            : $record->prospecto->razon_social;
                        return 'Realizar Tarea - ' . $nombreCompleto . ' - ' . $record->prospecto->celular;
                    })
                    ->modalWidth('4xl')
                    ->mountUsing(function ($record, $form) {
                        $nombre = $record->prospecto->nombres
                            ? $record->prospecto->nombres . ' ' . $record->prospecto->ape_paterno . ($record->prospecto->ape_materno ? ' ' . $record->prospecto->ape_materno : '')
                            : $record->prospecto->razon_social;

                        $form->fill([
                            'nombre' => $nombre,
                            'telefono' => $record->prospecto->celular,
                            'proyecto' => $record->prospecto->proyecto->nombre ?? 'No especificado',
                        ]);
                    })
                    ->form([
                        Card::make()->schema([
                            TextInput::make('nombre')->label('Nombre del Prospecto')->disabled(),
                            TextInput::make('telefono')->label('Teléfono/Celular')->disabled(),
                            TextInput::make('proyecto')->label('Proyecto de interés')->disabled(),
                            Radio::make('respuesta')
                                ->label('Resultado del contacto')
                                ->options([
                                    'efectiva' => 'EFECTIVA',
                                    'no_efectiva' => 'NO EFECTIVA'
                                ])
                                ->required()
                                ->inline(),
                            Textarea::make('comentario')
                                ->label('Comentarios adicionales')
                                ->placeholder('Detalles de la conversación')
                                ->rows(3)
                                ->maxLength(500),
                        ])
                    ])
                    ->action(function (Tarea $record, array $data) {
                        try {
                            if ($data['respuesta'] !== 'efectiva' && $record->prospecto->tipo_gestion_id == 3) {
                                Notification::make()
                                    ->title('Acción no permitida')
                                    ->body('No se puede retroceder de Contactados a Por Contactar')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $record->update([
                                'fecha_contacto' => now(),
                                'nota' => $data['comentario'] ?? null,
                            ]);

                            $nuevoTipoGestion = null;
                            if ($data['respuesta'] === 'efectiva') {
                                $nuevoTipoGestion = 3; // Contactados
                            } else if ($record->prospecto->tipo_gestion_id == 1) {
                                $nuevoTipoGestion = 2; // Por Contactar
                            }

                            if ($nuevoTipoGestion) {
                                $record->prospecto->update(['tipo_gestion_id' => $nuevoTipoGestion]);
                            }

                            Notification::make()
                                ->title('Tarea registrada correctamente')
                                ->success()
                                ->send();

                            return redirect(request()->header('Referer'));
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error al guardar los cambios')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                            throw $e;
                        }
                    }),

                Action::make('agendarCita')
                    ->label('Agendar Cita')
                    ->icon('heroicon-o-calendar')
                    ->color('success')
                    ->button()
                    ->size('sm')
                    ->visible(fn ($record) =>in_array($record->prospecto?->tipo_gestion_id, [3, 4]))
                    ->modalHeading(function ($record) {
                        $prospecto = $record->prospecto;
                        $nombreCompleto = $prospecto->nombres
                            ? $prospecto->nombres . ' ' . $prospecto->ape_paterno . ($prospecto->ape_materno ? ' ' . $prospecto->ape_materno : '')
                            : $prospecto->razon_social;

                        return 'Agendar Cita - ' . $nombreCompleto . ' - ' . $prospecto->celular;
                    })
                    ->modalWidth('4xl')
                    ->form([
                        Card::make()->schema([
                            TextInput::make('proyecto')
                                ->label('Proyecto')
                                ->default(fn ($record) => $record->prospecto->proyecto->nombre ?? '')
                                ->disabled(),

                            Radio::make('forma_contacto_id')
                                ->label('Forma de contacto')
                                ->view('filament.resources.panel-seguimiento-resource.custom-forma-contacto')
                                ->required(),

                            Grid::make(2)->schema([
                                DatePicker::make('fecha_cita')
                                    ->label('Fecha de Cita')
                                    ->required(),

                                TimePicker::make('hora_cita')
                                    ->label('Hora de Cita')
                                    ->required(),
                            ]),

                            Grid::make(2)->schema([
                                Select::make('responsable_id')
                                    ->label('Responsable')
                                    ->relationship('usuarioAsignado', 'name')
                                    ->searchable()
                                    ->required(),

                                TextInput::make('lugar')
                                    ->label('Lugar de Cita')
                                    ->placeholder('Ej: Oficina, Zoom, Google Meet, etc.')
                                    ->required(),
                            ]),

                            Radio::make('modalidad')
                                ->label('Modalidad')
                                ->options([
                                    'presencial' => 'Presencial',
                                    'virtual' => 'Virtual',
                                ])
                                ->inline()
                                ->required(),

                            Textarea::make('observaciones')
                                ->label('Observaciones')
                                ->rows(3)
                                ->maxLength(500),
                        ])
                    ])
                    ->action(function (Tarea $record, array $data) {
                        try {
                            $userId = auth()->id();

                            // Crear la nueva cita
                            $cita = \App\Models\Cita::create([
                                'tarea_id' => $record->id,
                                'proyecto_id' => $record->prospecto->proyecto_id,
                                'responsable_id' => $data['responsable_id'],
                                'fecha_cita' => $data['fecha_cita'],
                                'hora_cita' => $data['hora_cita'],
                                'modalidad' => $data['modalidad'],
                                'lugar' => $data['lugar'],
                                'observaciones' => $data['observaciones'] ?? null,                                                               
                                'created_by' => $userId,
                            ]);

                            // Crear nueva tarea asociada a la cita
                            \App\Models\Tarea::create([
                                'prospecto_id' => $record->prospecto->id,
                                'forma_contacto_id' => $data['forma_contacto_id'], 
                                'nivel_interes_id' => 5, 
                                'usuario_asignado_id' => auth()->id(),
                                'fecha_realizar' => now()->toDateString(),
                                'hora' => now()->format('H:i:s'),
                                'nota' => $data['nota'] ?? null,
                                'created_by' => auth()->id(),
                                'updated_by' => auth()->id(),
                            ]);

                            // Cambiar tipo de gestión del prospecto a "Citados"
                            $record->prospecto->update([
                                'tipo_gestion_id' => 4, // ID del estado "Citados"
                                'updated_by' => $userId,
                            ]);

                            Notification::make()
                                ->title('Cita agendada correctamente')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error al agendar la cita')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                            throw $e;
                        }
                    }),
                    Action::make('irAProforma')
                        ->label('Proforma')
                        ->icon('heroicon-o-document-text')
                        ->color('warning')
                        ->button()
                        ->size('sm')
                        ->visible(fn ($record) => $record->prospecto?->tipo_gestion_id === 4)
                        ->url(fn ($record) =>
                            ProformaResource::getUrl('create', ['numero_documento' => $record->prospecto->numero_documento])
                        )
                        ->openUrlInNewTab(),
                    Action::make('separacion')
                        ->label('Separación')
                        ->icon('heroicon-o-badge-check')
                        ->color('gray')
                        ->button()
                        ->size('sm')
                        ->visible(fn ($record) => $record->prospecto?->tipo_gestion_id === 5)
                        ->url(fn ($record) =>
                            SeparacionResource::getUrl('create', ['numero_documento' => $record->prospecto->numero_documento])
                        )
                        ->openUrlInNewTab(),
                    ],
                position: \Filament\Tables\Actions\Position::BeforeColumns);
    }


    public static function getWidgets(): array
    {
        return [
            SeguimientoFilters::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPanelSeguimientos::route('/'),
        ];
    }
}
