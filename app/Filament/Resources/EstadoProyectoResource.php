<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EstadoProyectoResource\Pages;
use App\Models\EstadoProyecto;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;

class EstadoProyectoResource extends Resource
{
    protected static ?string $model = EstadoProyecto::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    
    protected static ?string $modelLabel = 'Estado de Proyecto';
    
    protected static ?string $pluralModelLabel = 'Estados de Proyecto';


     protected static function getNavigationGroup(): ?string
    {
        return __('Management');
    }
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true)
                    ->label('Nombre del Estado')
                    ->placeholder('Ej: En construcción, Terminado, etc.'),
                    
                Textarea::make('descripcion')
                    ->maxLength(255)
                    ->label('Descripción')
                    ->columnSpanFull()
                    ->placeholder('Breve descripción del estado'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable()
                    ->sortable()
                    ->label('Nombre'),
                    
                TextColumn::make('descripcion')
                    ->limit(50)
                    ->searchable()
                    ->label('Descripción'),
                    
                TextColumn::make('proyectos_count')
                    ->counts('proyectos')
                    ->label('Proyectos')
                    ->sortable(),
            ])
          
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil'),
                    
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->before(function (EstadoProyecto $record, Tables\Actions\DeleteAction $action) {
                        if ($record->proyectos()->count() > 0) {
                            Notification::make()
                                ->danger()
                                ->title('No se puede eliminar')
                                ->body('Este estado tiene proyectos asociados')
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
                            if ($record->proyectos()->count() > 0) {
                                Notification::make()
                                    ->danger()
                                    ->title('No se puede eliminar')
                                    ->body("El estado {$record->nombre} tiene proyectos asociados")
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
        return [];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEstadoProyectos::route('/'),
            'create' => Pages\CreateEstadoProyecto::route('/create'),
            'edit' => Pages\EditEstadoProyecto::route('/{record}/edit'),
        ];
    }
    
    public static function getGloballySearchableAttributes(): array
    {
        return ['nombre', 'descripcion'];
    }
}