<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsultaStockResource\Pages;
use App\Models\Departamento;
use App\Models\EstadoDepartamento;
use App\Models\TipoInmueble;
use App\Models\Edificio;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class ConsultaStockResource extends Resource
{
    protected static ?string $model = Departamento::class;
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Stock por Piso';
    protected static ?string $modelLabel = 'Stock por Piso';
    protected static ?string $slug = 'stock-por-piso';
    protected static ?string $navigationGroup = 'Gestión Comercial';
    protected static ?int $navigationSort = 2;


    public static function table(Table $table): Table
    {
    return $table
        ->columns([
            // tus columnas aquí...
        ])
        ->filters([
            // tus filtros aquí...
        ])
        ->actions([])
        ->bulkActions([])
        ->defaultSort('tipo_inmueble_id');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConsultaStocks::route('/'),
        ];
    }


}
