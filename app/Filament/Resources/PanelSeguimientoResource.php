<?php

namespace App\Filament\Resources;

use App\Models\Tarea;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use App\Models\FormaContacto;
use App\Models\User;
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
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use App\Filament\Resources\PanelSeguimientoResource\Pages;
use App\Filament\Resources\PanelSeguimientoResource\Widgets\SeguimientoFilters;
use App\Filament\Resources\Proforma\ProformaResource;
use App\Filament\Resources\Separaciones\SeparacionResource;

use Illuminate\Support\HtmlString;
use App\Filament\Resources\PanelSeguimientoResource\Pages\ViewProspectoInfo;

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
                TextColumn::make('prospecto.id')->label('ID')->searchable(),
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
                    ->url(fn ($record) => PanelSeguimientoResource::getUrl('view', ['record' => $record->prospecto_id]))
                    ->openUrlInNewTab()
                    ->searchable(),

                TextColumn::make('prospecto.celular')
                ->label('Teléfono')
                ->searchable()
                ->formatStateUsing(function ($state, $record) {
                    $numero = preg_replace('/[^0-9]/', '', $record->prospecto->celular);
                    $whatsappUrl = 'https://web.whatsapp.com/send?phone=' . $numero;
                    return new \Illuminate\Support\HtmlString(
                        '<div class="flex items-center gap-2">' .
                        '<a href="' . $whatsappUrl . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">' . $state . '</a>' .
                        '<a href="' . $whatsappUrl . '" target="_blank" class="text-green-500 hover:text-green-700 inline-flex items-center">' .
                        '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">' .
                        '<path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>' .
                        '</svg>' .
                        '</a>' .
                        '</div>'
                    );
                }),
                TextColumn::make('prospecto.numero_documento')->label('N° Doc.')->searchable(),
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
                    ->visible(fn ($record) =>in_array($record->prospecto?->tipo_gestion_id, [1, 2, 3, 4, 5, 6]))
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
                        Grid::make(2)->schema([

                            // Columna izquierdanp
                            Card::make()->schema([
                                Grid::make(3)->schema([
                                    TextInput::make('nombre')->label('Nombre del Prospecto')->disabled(),
                                    TextInput::make('telefono')->label('Teléfono/Celular')->disabled(),
                                    TextInput::make('proyecto')->label('Proyecto de interés')->disabled(),
                                ]),
                                Radio::make('forma_contacto_id')
                                    ->label('Forma de contacto')
                                    ->view('filament.resources.panel-seguimiento-resource.custom-forma-contacto')
                                    ->required(),
                                Grid::make(2)->schema([
                                    DatePicker::make('fecha_realizar')
                                        ->label('Fecha acción')
                                        ->required()
                                        ->afterStateHydrated(fn ($set, $state) => $set('fecha_realizar', $state ?? now()->toDateString())),
                                    TextInput::make('hora')
                                        ->label('Hora acción')
                                        ->type('time')
                                        ->required()
                                        ->default(now()->format('H:i'))
                                        ->afterStateHydrated(fn ($set, $state) => $set('hora', $state ?? now()->format('H:i')))
                                ]),
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
                                Radio::make('nivel_interes_id')
                                    ->label('Nivel de Interés')
                                    ->options(NivelInteres::pluck('nombre', 'id'))
                                    ->inline()
                                    ->required(),
                                Placeholder::make('ultima_accion')
                                    ->label('')
                                    ->content(function ($record) {
                                        $prospecto = $record?->prospecto ?? null;

                                        if (!$prospecto) {
                                            return new HtmlString('<hr class="my-4"><div class="text-sm text-gray-500 font-medium">Última Acción</div><div class="text-sm text-gray-500">Sin acciones anteriores.</div>');
                                        }

                                        $ultimaTarea = $prospecto->tareas()->latest('created_at')->first();

                                        if (!$ultimaTarea) {
                                            return new HtmlString('<hr class="my-4"><div class="text-sm text-gray-500 font-medium">Última Acción</div><div class="text-sm text-gray-500">Sin acciones anteriores.</div>');
                                        }

                                        return new HtmlString('
                                            <hr class="my-4">
                                            <div class="text-base font-semibold text-gray-800 mb-2">Última Acción</div>
                                            <div class="space-y-2 text-sm text-gray-700 leading-snug">
                                                <div class="flex flex-wrap gap-4">
                                                    <div><strong>Forma de contacto: </strong> ' . e($ultimaTarea->formaContacto?->nombre) . '</div>
                                                    <div><strong>Fecha: </strong> ' . $ultimaTarea->fecha_realizar->format('d/m/Y') . '</div>
                                                    <div><strong>Hora: </strong> ' . $ultimaTarea->hora . '</div>
                                                </div>
                                                <div><strong>Nivel de interés:</strong> ' . e($ultimaTarea->nivelInteres?->nombre) . '</div>
                                                <div><strong>Nota:</strong> ' . e($ultimaTarea->nota) . '</div>
                                            </div>
                                        ');
                                    }),
                            ]), // Fin columna izquierda

                            // Columna derecha
                            Card::make()->schema([
                                Toggle::make('crear_proxima_tarea')
                                    ->label('¿Crear próxima tarea?')
                                    ->default(true)
                                    ->hidden()
                                    ->afterStateHydrated(function ($component, $state) {
                                        $component->state(true);
                                    }),

                                Select::make('proxima_usuario_asignado_id')
                                    ->label('Asignar a')
                                    ->options(User::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->required(),
                                Radio::make('proxima_forma_contacto_id')
                                    ->label('Forma de contacto')
                                    ->view('filament.resources.panel-seguimiento-resource.custom-forma-contacto'),
                                Grid::make(2)->schema([
                                    DatePicker::make('proxima_fecha')
                                        ->label('Fecha próxima tarea')
                                        ->default(now()->addDays(1)),

                                    TextInput::make('proxima_hora')
                                        ->label('Hora próxima tarea')
                                        ->type('time')
                                        ->default('18:00')
                                        ->afterStateHydrated(fn ($set, $state) => $set('proxima_hora', $state ?? '18:00'))
                                ]),
                            ]), // Fin columna derecha

                        ])
                    ])
                    ->action(function (Tarea $record, array $data, $livewire) {
                        try {
                            $prospecto = $record->prospecto;
                            if ($data['respuesta'] !== 'efectiva' && $record->prospecto->tipo_gestion_id == 3) {
                                Notification::make()
                                    ->title('Acción no permitida')
                                    ->body('No se puede retroceder de Contactados a Por Contactar')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            Tarea::create([
                                'prospecto_id' => $prospecto->id,
                                'forma_contacto_id' => $data['forma_contacto_id'],
                                'fecha_realizar' => $data['fecha_realizar'],
                                'hora' => $data['hora'],
                                'nota' => $data['comentario'] ?? null,
                                'nivel_interes_id' => $data['nivel_interes_id'],
                                'usuario_asignado_id' => auth()->id(),
                                'created_by' => auth()->id(),
                                'updated_by' => auth()->id(),
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

                            if (!empty($data['crear_proxima_tarea']) && $data['proxima_fecha'] && $data['proxima_forma_contacto_id']) {
                                Tarea::create([
                                    'prospecto_id' => $prospecto->id,
                                    'tarea_padre_id' => $record->id, // Aquí se guarda la trazabilidad
                                    'forma_contacto_id' => $data['proxima_forma_contacto_id'],
                                    'fecha_realizar' => $data['proxima_fecha'],
                                    'hora' => $data['proxima_hora'] ?? '09:00:00', // valor por defecto si no se define
                                    'nota' => $data['proxima_comentario'] ?? null,
                                    'nivel_interes_id' => $data['nivel_interes_id'], // reutilizamos el mismo nivel de interés
                                    'usuario_asignado_id' => auth()->id(),
                                    'created_by' => auth()->id(),
                                    'updated_by' => auth()->id(),
                                ]);
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
                    ->visible(fn ($record) =>in_array($record->prospecto?->tipo_gestion_id, [1, 2, 3, 4, 5]))
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
                                    ->options(User::all()->pluck('name', 'id'))
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

                            return redirect(request()->header('Referer'));

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
                        ->visible(fn ($record) =>in_array($record->prospecto?->tipo_gestion_id, [1, 2, 3, 4, 5]))
                        ->url(fn ($record) =>
                            ProformaResource::getUrl('create', ['prospecto_id' => $record->prospecto->id])
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
            'view' => ViewProspectoInfo::route('/prospecto/{record}'),
        ];
    }
}