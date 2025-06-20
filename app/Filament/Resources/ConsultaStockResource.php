<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsultaStockResource\Pages;
use App\Filament\Resources\ConsultaStockResource\Widgets;
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('edificio.nombre')
                    ->label('Edificio')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('piso')
                    ->label('Piso')
                    ->sortable(),
                    
                TextColumn::make('numero')
                    ->label('Departamento')
                    ->sortable(),
                    
                BadgeColumn::make('estado.nombre')
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
                Tables\Filters\SelectFilter::make('edificio')
                    ->relationship('edificio', 'nombre'),
                    
                Tables\Filters\SelectFilter::make('estado')
                    ->relationship('estado', 'nombre')
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

    protected function applyEstadoFilter(Builder $query, string $estado): Builder
    {
        return $query->whereHas('estado', fn($q) => $q->where('nombre', $estado));
    }

    public static function getRelations(): array
    {
        return [];
    }
    
    public static function getWidgets(): array
    {
        return [
            ConsultaStockResource\Widgets\EstadosDepartamentoWidget::class,
            ConsultaStockResource\Widgets\EstadosInmuebleWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConsultaStocks::route('/'),
        ];
    }
}