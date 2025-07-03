<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EstadoDepartamentoResource\Pages;
use App\Models\EstadoDepartamento;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Notifications\Notification;

class EstadoDepartamentoResource extends Resource
{
    protected static ?string $model = EstadoDepartamento::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $modelLabel = 'Estado de Departamento';

    protected static ?string $pluralModelLabel = 'Estados de Departamento';

    protected static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                TextInput::make('nombre')
                                    ->required()
                                    ->maxLength(50)
                                    ->unique(ignoreRecord: true)
                                    ->label('Nombre del Estado')
                                    ->placeholder('Ej: Disponible, Ocupado, Mantenimiento'),

                                Forms\Components\ColorPicker::make('color')
                                    ->label('Color del Estado')
                                    ->required()
                                    ->default('#6b7280'), // Color gris por defecto

                                Forms\Components\Checkbox::make('is_default')
                                    ->label('Estado por defecto')
                                    ->helperText('Si está marcado, será el estado asignado por defecto a nuevos departamentos'),
                            ]),

                        Textarea::make('descripcion')
                            ->maxLength(255)
                            ->label('Descripción')
                            ->columnSpanFull()
                            ->placeholder('Breve descripción del estado'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ColorColumn::make('color')
                    ->label('Color')
                    ->sortable(),

                TextColumn::make('nombre')
                    ->searchable()
                    ->sortable()
                    ->label('Nombre'),

                TextColumn::make('descripcion')
                    ->limit(50)
                    ->searchable()
                    ->label('Descripción'),

                Tables\Columns\IconColumn::make('is_default')
                    ->label('Por defecto')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('departamentos_count')
                    ->counts('departamentos')
                    ->label('Departamentos')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil'),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->before(function (EstadoDepartamento $record, Tables\Actions\DeleteAction $action) {
                        if ($record->departamentos()->count() > 0) {
                            Notification::make()
                                ->danger()
                                ->title('No se puede eliminar')
                                ->body('Este estado tiene departamentos asociados')
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
                                    ->body("El estado {$record->nombre} tiene departamentos asociados")
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
            'index' => Pages\ListEstadoDepartamentos::route('/'),
            'create' => Pages\CreateEstadoDepartamento::route('/create'),
            'edit' => Pages\EditEstadoDepartamento::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['nombre', 'descripcion'];
    }
}
