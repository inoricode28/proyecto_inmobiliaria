<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProyectoResource\Pages;
use App\Filament\Resources\ProyectoResource\RelationManagers;
use App\Models\Proyecto;
use App\Models\EstadoProyecto;
use App\Models\Empresa;
use Filament\Forms;
use Filament\Forms\Components\Card;
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

class ProyectoResource extends Resource
{
    protected static ?string $model = Proyecto::class;
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-folder';
    
    protected static ?string $modelLabel = 'Proyecto';
    
    protected static ?string $pluralModelLabel = 'Proyectos';
    
 protected static function getNavigationGroup(): ?string
    {
        return __('Management');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    TextInput::make('nombre')
                        ->required()
                        ->maxLength(100)
                        ->label('Nombre del Proyecto'),
                        
                    Textarea::make('descripcion')
                        ->label('Descripción')
                        ->columnSpanFull(),
                        
                    TextInput::make('ubicacion')
                        ->required()
                        ->maxLength(255)
                        ->label('Ubicación'),
                        
                    Select::make('estado_proyecto_id')
                        ->label('Estado del Proyecto')
                        ->options(EstadoProyecto::all()->pluck('nombre', 'id'))
                        ->required()
                        ->searchable(),
                        
                    Select::make('empresa_constructora_id')
                        ->label('Empresa Constructora')
                        ->options(Empresa::all()->pluck('nombre', 'id'))
                        ->required()
                        ->searchable(),
                        
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
                    
                TextColumn::make('estado.nombre')
                    ->searchable()
                    ->label('Estado'),
                    
                TextColumn::make('empresa.nombre')
                    ->searchable()
                    ->label('Empresa'),
                    
                TextColumn::make('fecha_inicio')
                    ->date()
                    ->sortable(),
                    
                TextColumn::make('fecha_entrega')
                    ->date()
                    ->sortable(),
                    
                TextColumn::make('edificios_count')
                    ->counts('edificios')
                    ->label('Edificios')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado_proyecto_id')
                    ->label('Estado')
                    ->options(EstadoProyecto::all()->pluck('nombre', 'id')),
                    
                Tables\Filters\SelectFilter::make('empresa_constructora_id')
                    ->label('Empresa')
                    ->options(Empresa::all()->pluck('nombre', 'id')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil'),
                    
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->before(function (Proyecto $record, Tables\Actions\DeleteAction $action) {
                        if ($record->edificios()->count() > 0) {
                            Notification::make()
                                ->danger()
                                ->title('No se puede eliminar')
                                ->body('Este proyecto tiene edificios asociados')
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
                            if ($record->edificios()->count() > 0) {
                                Notification::make()
                                    ->danger()
                                    ->title('No se puede eliminar')
                                    ->body("El proyecto {$record->nombre} tiene edificios asociados")
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
          //  RelationManagers\EdificiosRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProyectos::route('/'),
            'create' => Pages\CreateProyecto::route('/create'),
            'edit' => Pages\EditProyecto::route('/{record}/edit'),
        ];
    }
    
    public static function getGloballySearchableAttributes(): array
    {
        return ['nombre', 'descripcion', 'ubicacion'];
    }
}