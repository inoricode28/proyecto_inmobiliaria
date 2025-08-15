<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntregaResource\Pages;
use App\Models\Entrega;
use App\Models\Venta;
use App\Models\Prospecto;
use App\Models\Departamento;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class EntregaResource extends Resource
{
    protected static ?string $model = Entrega::class;
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $modelLabel = 'Entrega';
    protected static ?string $pluralModelLabel = 'Entregas';
    protected static ?string $navigationGroup = 'Ventas';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Sección Cliente Titular
            Section::make('CLIENTE TITULAR')
                ->description('Información del cliente')
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('prospecto.nombres')
                            ->label('Nombre Completo')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(function ($record) {
                                if ($record && $record->prospecto) {
                                    return $record->nombre_completo_prospecto;
                                }
                                return '';
                            })
                            ->columnSpan(2),
                            
                        TextInput::make('prospecto.documento')
                            ->label('DNI')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(function ($record) {
                                return $record?->prospecto?->documento ?? '';
                            })
                            ->columnSpan(1),
                    ]),
                    
                    Grid::make(3)->schema([
                        TextInput::make('prospecto.telefono')
                            ->label('Teléfono')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(function ($record) {
                                return $record?->prospecto?->telefono ?? '';
                            })
                            ->columnSpan(1),
                            
                        TextInput::make('prospecto.celular')
                            ->label('Celular')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(function ($record) {
                                return $record?->prospecto?->celular ?? '';
                            })
                            ->columnSpan(1),
                            
                        TextInput::make('prospecto.email')
                            ->label('E-mail')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(function ($record) {
                                return $record?->prospecto?->email ?? '';
                            })
                            ->columnSpan(1),
                    ]),
                    
                    TextInput::make('prospecto.direccion')
                        ->label('Dirección')
                        ->disabled()
                        ->dehydrated(false)
                        ->formatStateUsing(function ($record) {
                            return $record?->prospecto?->direccion ?? '';
                        })
                        ->columnSpanFull(),
                ])
                ->collapsible()
                ->columnSpan('full'),
                
            // Sección Inmuebles
            Section::make('INMUEBLES')
                ->description('Información del inmueble')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('venta_id')
                            ->label('Venta')
                            ->relationship('venta', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "Venta #{$record->id}")
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                if ($state) {
                                    $venta = Venta::find($state);
                                    if ($venta && $venta->separacion && $venta->separacion->proforma) {
                                        $set('prospecto_id', $venta->separacion->proforma->prospecto_id);
                                        $set('departamento_id', $venta->separacion->proforma->departamento_id);
                                    }
                                }
                            })
                            ->columnSpan(1),
                            
                        Select::make('prospecto_id')
                            ->label('Prospecto')
                            ->relationship('prospecto', 'nombres')
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                $tieneNombre = $record->nombres && $record->ape_paterno;
                                return $tieneNombre
                                    ? $record->nombres . ' ' . $record->ape_paterno . ' ' . ($record->ape_materno ?? '')
                                    : ($record->razon_social ?? '-');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),
                    ]),
                    
                    TextInput::make('departamento.info')
                        ->label('Inmuebles')
                        ->disabled()
                        ->dehydrated(false)
                        ->formatStateUsing(function ($record) {
                            if ($record && $record->departamento) {
                                $edificio = $record->departamento->proyecto->nombre ?? 'N/A';
                                $modelo = $record->departamento->tipo_departamento->nombre ?? 'N/A';
                                $depto = $record->departamento->num_departamento;
                                return "Edificio: {$edificio} / MODELO: {$modelo} - Departamento {$depto}";
                            }
                            return '';
                        })
                        ->columnSpanFull(),
                        
                    Select::make('departamento_id')
                        ->label('Departamento')
                        ->relationship('departamento', 'num_departamento')
                        ->getOptionLabelFromRecordUsing(fn ($record) => "Depto. {$record->num_departamento} - {$record->proyecto->nombre}")
                        ->searchable()
                        ->preload()
                        ->required()
                        ->hidden(),
                ])
                ->collapsible()
                ->columnSpan('full'),
                
            // Sección Entrega de Inmuebles
            Section::make('ENTREGA DE INMUEBLES')
                ->description('Fechas y detalles de la entrega')
                ->schema([
                    Grid::make(3)->schema([
                        DatePicker::make('fecha_entrega')
                            ->label('Fecha')
                            ->displayFormat('d/m/Y')
                            ->format('Y-m-d')
                            ->required()
                            ->columnSpan(1),

                        DatePicker::make('fecha_garantia_acabados')
                            ->label('Fecha Garantía Acabados')
                            ->displayFormat('d/m/Y')
                            ->format('Y-m-d')
                            ->nullable()
                            ->columnSpan(1),

                        DatePicker::make('fecha_garantia_vicios_ocultos')
                            ->label('Fecha Garantía Vicios Ocultos')
                            ->displayFormat('d/m/Y')
                            ->format('Y-m-d')
                            ->nullable()
                            ->columnSpan(1),
                    ]),

                    Textarea::make('descripcion')
                        ->label('Descripción')
                        ->rows(4)
                        ->columnSpanFull(),
                        
                    Grid::make(2)->schema([
                        TextInput::make('createdBy.name')
                            ->label('Registrado por')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(function ($record) {
                                return $record?->createdBy?->name ?? '';
                            })
                            ->columnSpan(1),
                            
                        // Aquí podrías agregar un botón de "GRABAR" o "IMPRIMIR" si es necesario
                    ]),
                ])
                ->columnSpan('full'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('venta.id')
                    ->label('Venta ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('prospecto.nombres')
                    ->label('Prospecto')
                    ->formatStateUsing(function ($record) {
                        return $record->nombre_completo_prospecto;
                    })
                    ->searchable(['prospectos.nombres', 'prospectos.ape_paterno', 'prospectos.razon_social'])
                    ->sortable(),

                TextColumn::make('departamento.num_departamento')
                    ->label('Departamento')
                    ->formatStateUsing(fn ($record) => "Depto. {$record->departamento->num_departamento}")
                    ->sortable()
                    ->searchable(),

                TextColumn::make('departamento.proyecto.nombre')
                    ->label('Proyecto')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('fecha_entrega')
                    ->label('Fecha Entrega')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('fecha_garantia_acabados')
                    ->label('Garantía Acabados')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('fecha_garantia_vicios_ocultos')
                    ->label('Garantía Vicios Ocultos')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEntregas::route('/'),
            'create' => Pages\CreateEntrega::route('/create'),
            'view' => Pages\ViewEntrega::route('/{record}'),
            'edit' => Pages\EditEntrega::route('/{record}/edit'),
        ];
    }
}