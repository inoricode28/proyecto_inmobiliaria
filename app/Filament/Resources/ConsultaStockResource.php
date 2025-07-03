<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsultaStockResource\Pages;
use App\Models\Departamento;
use App\Models\EstadoDepartamento;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
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
                // Asegurémonos de acceder correctamente a las relaciones
                TextColumn::make('edificio.nombre')  // Relación de edificio
                    ->label('Edificio')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('num_piso')  // Columna para Piso
                    ->label('Piso')
                    ->sortable(),

                TextColumn::make('num_departamento')  // Columna para Departamento
                    ->label('Departamento')
                    ->sortable(),

                BadgeColumn::make('estadoDepartamento.nombre')  // Relación con estado
                    ->label('Estado')
                    ->colors([
                        'Disponible' => 'success',
                        'Separación temporal' => 'warning',
                        'Pagando sin minuta' => 'info',
                        'Bloqueado' => 'danger',
                        'Entregado' => 'primary',
                        0 => 'gray',
                    ]),
            ])
            ->filters([
                // Filtros para la tabla
                Tables\Filters\SelectFilter::make('edificio_id')
                    ->relationship('edificio', 'nombre'),

                Tables\Filters\SelectFilter::make('estado_departamento_id')
                    ->relationship('estadoDepartamento', 'nombre')
                    ->options(function () {
                        return EstadoDepartamento::all()->pluck('nombre', 'nombre');
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    // Método para aplicar el filtro por estado
    protected function applyEstadoFilter(Builder $query, string $estado): Builder
    {
        return $query->whereHas('estadoDepartamento', fn($q) => $q->where('nombre', $estado));
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConsultaStocks::route('/'),
        ];
    }

    
}
