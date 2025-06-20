<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepartamentoResource\Pages;
use App\Filament\Resources\DepartamentoResource\RelationManagers;
use App\Models\Departamento;
use App\Models\Edificio;
use App\Models\TipoDepartamento;
use App\Models\EstadoDepartamento;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Card;

class DepartamentoResource extends Resource
{
    protected static ?string $model = Departamento::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Departamento';
    
    protected static ?string $pluralModelLabel = 'Departamentos';
    
 protected static function getNavigationGroup(): ?string
    {
        return __('Management');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Select::make('edificio_id')
                        ->label('Edificio')
                        ->options(Edificio::all()->pluck('nombre', 'id'))
                        ->required()
                        ->searchable(),
                        
                    TextInput::make('numero')
                        ->required()
                        ->maxLength(20)
                        ->label('Número de departamento'),
                        
                    TextInput::make('piso')
                        ->required()
                        ->numeric()
                        ->label('Piso'),
                        
                    TextInput::make('area_total')
                        ->required()
                        ->numeric()
                        ->label('Área total (m²)'),
                        
                    TextInput::make('area_construida')
                        ->required()
                        ->numeric()
                        ->label('Área construida (m²)'),
                        
                    TextInput::make('numero_habitaciones')
                        ->required()
                        ->numeric()
                        ->label('Número de habitaciones'),
                        
                    TextInput::make('numero_banos')
                        ->required()
                        ->numeric()
                        ->label('Número de baños'),
                        
                    Toggle::make('tiene_balcon')
                        ->label('¿Tiene balcón?')
                        ->required(),
                        
                    Select::make('tipo_departamento_id')
                        ->label('Tipo de departamento')
                        ->options(TipoDepartamento::all()->pluck('nombre', 'id'))
                        ->required()
                        ->searchable(),
                        
                    Select::make('estado_departamento_id')
                        ->label('Estado del departamento')
                        ->options(EstadoDepartamento::all()->pluck('nombre', 'id'))
                        ->required()
                        ->searchable(),
                        
                    TextInput::make('precio')
                        ->required()
                        ->numeric()
                        ->prefix('$')
                        ->label('Precio'),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('numero')
                    ->searchable()
                    ->sortable()
                    ->label('Número'),
                    
                TextColumn::make('edificio.nombre')
                    ->searchable()
                    ->sortable()
                    ->label('Edificio'),
                    
                TextColumn::make('piso')
                    ->sortable()
                    ->label('Piso'),
                    
                TextColumn::make('tipo.nombre')
                    ->searchable()
                    ->label('Tipo'),
                    
                TextColumn::make('estado.nombre')
                    ->searchable()
                    ->label('Estado'),
                    
               TextColumn::make('precio')
                ->formatStateUsing(function ($state) {
                    return '$' . number_format($state, 2, '.', ',');
                })
                ->sortable()
                ->label('Precio'),
               
                    
                TextColumn::make('area_total')
                    ->suffix(' m²')
                    ->sortable()
                    ->label('Área total'),
            ])
            ->filters([
                SelectFilter::make('edificio_id')
                    ->label('Edificio')
                    ->options(Edificio::all()->pluck('nombre', 'id')),
                    
                SelectFilter::make('tipo_departamento_id')
                    ->label('Tipo')
                    ->options(TipoDepartamento::all()->pluck('nombre', 'id')),
                    
                SelectFilter::make('estado_departamento_id')
                    ->label('Estado')
                    ->options(EstadoDepartamento::all()->pluck('nombre', 'id')),
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

    public static function getRelations(): array
    {
        return [
            // Puedes agregar RelationManagers aquí si es necesario
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepartamentos::route('/'),
            'create' => Pages\CreateDepartamento::route('/create'),
            'edit' => Pages\EditDepartamento::route('/{record}/edit'),
        ];
    }
    
    public static function getGloballySearchableAttributes(): array
    {
        return ['numero', 'edificio.nombre'];
    }
}