<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TipoFinanciamientoResource\Pages;
use App\Models\TipoFinanciamiento;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class TipoFinanciamientoResource extends Resource
{
    protected static ?string $model = TipoFinanciamiento::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $modelLabel = 'Tipos de Financiamiento';
    protected static ?string $pluralModelLabel = 'Tipos de Financiamiento';

    protected static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make()->schema([
                TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(100)
                    ->unique(
                        TipoFinanciamiento::class,
                        'nombre',
                        ignoreRecord: true 
                    )
                    ->columnSpan(1),

                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->nullable()
                    ->maxLength(255)
                    ->columnSpan(1),

                ColorPicker::make('color')
                    ->label('Color')
                    ->nullable()
                    ->default('#6b7280')
                    ->columnSpan(1),

                Toggle::make('is_default')
                    ->label('Por Defecto')
                    ->inline(false)
                    ->default(false)
                    ->columnSpan(1),
            ])->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->sortable()
                    ->searchable(),

                ColorColumn::make('color')
                    ->label('Color')
                    ->sortable(),

                BooleanColumn::make('is_default')
                    ->label('Por Defecto')
                    ->sortable()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                SelectFilter::make('is_default')
                    ->label('Por Defecto')
                    ->options([
                        1 => 'Sí',
                        0 => 'No',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil'),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTipoFinanciamientos::route('/'),
            'create' => Pages\CreateTipoFinanciamiento::route('/create'),
            'edit' => Pages\EditTipoFinanciamiento::route('/{record}/edit'),
        ];
    }
}
