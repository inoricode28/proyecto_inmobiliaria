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
use Filament\Notifications\Notification;
use App\Filament\Resources\PanelSeguimientoResource\Pages;
use App\Filament\Resources\PanelSeguimientoResource\Widgets\SeguimientoFilters;

class PanelSeguimientoResource extends Resource
{
    protected static ?string $model = Tarea::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-check';
    protected static ?string $navigationLabel = 'Panel de Seguimiento';
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
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

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


                TextColumn::make('prospecto.celular')
                    ->label('Teléfono')
                    ->searchable(),

                TextColumn::make('prospecto.proyecto.nombre')
                    ->label('Proyecto'),

                TextColumn::make('prospecto.comoSeEntero.nombre')
                    ->label('Cómo se enteró'),

                TextColumn::make('prospecto.fecha_registro')
                    ->label('Fec. Registro')
                    ->date('d/m/Y'),

                TextColumn::make('fecha_contacto')
                    ->label('Fec. Últ. Contacto')
                    ->dateTime('d/m/Y H:i'),

                TextColumn::make('fecha_realizar')
                    ->label('Fec. Tarea')
                    ->dateTime('d/m/Y'),

                TextColumn::make('usuarioAsignado.name')
                    ->label('Responsable'),
            ])
            ->actions([
                    Action::make('realizarTarea')
                        ->label('Realizar Tarea')
                        ->icon('heroicon-o-clipboard-check')
                        ->color('success')
                        ->button()
                        ->size('sm')
                        ->extraAttributes(['class' => 'mr-2'])
                        ->modalHeading(function ($record) {
                            // Obtener nombre completo o razón social
                            $nombreCompleto = $record->prospecto->nombres
                                ? $record->prospecto->nombres . ' ' . $record->prospecto->ape_paterno . ($record->prospecto->ape_materno ? ' ' . $record->prospecto->ape_materno : '')
                                : $record->prospecto->razon_social;

                            // Combinar con teléfono
                            return 'Realizar Tarea - ' . $nombreCompleto . ' - ' . $record->prospecto->celular;
                        })
                        ->modalWidth('4xl')
                        ->mountUsing(function ($record, $form) {
                            // Determinar qué nombre mostrar
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
                            Card::make()
                                ->schema([
                                    TextInput::make('nombre')
                                        ->label('Nombre del Prospecto')
                                        ->disabled(),

                                    TextInput::make('telefono')
                                        ->label('Teléfono/Celular')
                                        ->disabled(),

                                    TextInput::make('proyecto')
                                        ->label('Proyecto de interés')
                                        ->disabled(),

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
/*
                                    DateTimePicker::make('fecha_tarea')
                                        ->label('Próximo contacto')
                                        ->required()
                                        ->minDate(now())
                                        ->displayFormat('d/m/Y H:i'),
                                        */
                                ])
                        ])
                        ->action(function (Tarea $record, array $data) {
                        try {
                            // Validar si intenta retroceder de Contactados a Por Contactar
                            if ($data['respuesta'] !== 'efectiva'
                                && $record->prospecto->tipo_gestion_id == 3) {
                                Notification::make()
                                    ->title('Acción no permitida')
                                    ->body('No se puede retroceder de Contactados a Por Contactar')
                                    ->danger()
                                    ->send();
                                return; // Detener la ejecución
                            }

                            // Actualizar la tarea
                            $record->update([
                                'fecha_contacto' => now(),
                                'nota' => $data['comentario'] ?? null,
                            ]);

                            // Determinar el nuevo tipo de gestión según la respuesta
                            $nuevoTipoGestion = null;
                            $updateData = []; // Inicializar array de actualización

                            if ($data['respuesta'] === 'efectiva') {
                                $nuevoTipoGestion = 3; // Contactados
                            } else {
                                // Solo cambiar a Por Contactar si está en No gestionado (1)
                                if ($record->prospecto->tipo_gestion_id == 1) {
                                    $nuevoTipoGestion = 2; // Por Contactar
                                }
                            }

                            // Actualizar el prospecto si hay cambio de estado
                            if ($nuevoTipoGestion) {
                                $updateData['tipo_gestion_id'] = $nuevoTipoGestion;
                                $record->prospecto->update($updateData);
                            }

                            Notification::make()
                                ->title('Tarea registrada correctamente')
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error al guardar los cambios')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                            throw $e;
                        }
                    })
            ], position: \Filament\Tables\Actions\Position::BeforeColumns); // Posiciona las acciones a la izquierda
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