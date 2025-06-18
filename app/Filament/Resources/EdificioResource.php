<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EdificioResource\Pages;
use App\Filament\Resources\EdificioResource\RelationManagers;
use App\Models\Edificio;
use App\Models\Proyecto;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class EdificioResource extends Resource
{
    protected static ?string $model = Edificio::class;

    protected static ?string $navigationIcon = 'heroicon-o-office-building';

    protected static ?string $modelLabel = 'Edificio';
    
    protected static ?string $pluralModelLabel = 'Edificios';
    
 protected static function getNavigationGroup(): ?string
    {
        return __('Management');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Select::make('proyecto_id')
                        ->label('Proyecto')
                        ->options(Proyecto::all()->pluck('nombre', 'id'))
                        ->required()
                        ->searchable(),
                        
                    TextInput::make('nombre')
                        ->required()
                        ->maxLength(100)
                        ->label('Nombre del Edificio'),
                        
                    Textarea::make('descripcion')
                        ->label('Descripción')
                        ->columnSpanFull(),
                        
                    TextInput::make('cantidad_pisos')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->label('Número de Pisos'),
                        
                    TextInput::make('cantidad_departamentos')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->label('Número de Departamentos'),
                        
                    DatePicker::make('fecha_inicio')
                        ->required()
                        ->label('Fecha de Inicio'),
                        
                    DatePicker::make('fecha_entrega')
                        ->required()
                        ->label('Fecha de Entrega')
                        ->minDate(fn (callable $get) => $get('fecha_inicio')),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('proyecto.nombre')
                    ->searchable()
                    ->sortable()
                    ->label('Proyecto'),
                    
                TextColumn::make('cantidad_pisos')
                    ->sortable()
                    ->label('Pisos'),
                    
                TextColumn::make('cantidad_departamentos')
                    ->sortable()
                    ->label('Departamentos'),
                    
                TextColumn::make('fecha_inicio')
                    ->date()
                    ->sortable(),
                    
                TextColumn::make('fecha_entrega')
                    ->date()
                    ->sortable(),
                    
                TextColumn::make('departamentos_count')
                    ->counts('departamentos')
                    ->label('Deptos. Registrados')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('proyecto_id')
                    ->label('Proyecto')
                    ->options(Proyecto::all()->pluck('nombre', 'id')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil'),
                    
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->before(function (Edificio $record, Tables\Actions\DeleteAction $action) {
                        if ($record->departamentos()->count() > 0) {
                            Notification::make()
                                ->danger()
                                ->title('No se puede eliminar')
                                ->body('Este edificio tiene departamentos asociados')
                                ->persistent()
                                ->send();
                            $action->cancel();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->before(function ($action, $records) {
                        foreach ($records as $record) {
                            if ($record->departamentos()->count() > 0) {
                                Notification::make()
                                    ->danger()
                                    ->title('No se puede eliminar')
                                    ->body("El edificio {$record->nombre} tiene departamentos asociados")
                                    ->persistent()
                                    ->send();
                                $action->cancel();
                                break;
                            }
                        }
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //RelationManagers\DepartamentosRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEdificios::route('/'),
            'create' => Pages\CreateEdificio::route('/create'),
            'edit' => Pages\EditEdificio::route('/{record}/edit'),
        ];
    }
    
    public static function getGloballySearchableAttributes(): array
    {
        return ['nombre', 'descripcion', 'proyecto.nombre'];
    }
}