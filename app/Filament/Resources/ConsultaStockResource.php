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


    public static function getEloquentQuery(): Builder
{
    $query = Departamento::query()
        ->selectRaw('MIN(id) as id, tipo_inmueble_id')
        ->groupBy('tipo_inmueble_id');

    if ($edificioId = request()->get('tableFilters')['edificio_id']['value'] ?? null) {
        $query->where('edificio_id', $edificioId);
    }

    return $query;
}


    public static function table(Table $table): Table
    {
        $estados = EstadoDepartamento::orderBy('nombre')->get();

        // Filtrado por edificio
        $filtroEdificioId = request()->get('tableFilters')['edificio_id']['value'] ?? null;

        // Preconteo: tipo_inmueble_id x estado_departamento_id
        $agrupados = Departamento::query()
            ->when($filtroEdificioId, fn ($q) => $q->where('edificio_id', $filtroEdificioId))
            ->selectRaw('tipo_inmueble_id, estado_departamento_id, COUNT(*) as count')
            ->groupBy('tipo_inmueble_id', 'estado_departamento_id')
            ->get()
            ->groupBy('tipo_inmueble_id');

        // Columnas de estado dinámicas
        $estadoColumns = [];
        foreach ($estados as $estado) {
            $estadoColumns[] = TextColumn::make('estado_' . $estado->id)
                ->label($estado->nombre)
                ->getStateUsing(function ($record) use ($agrupados, $estado) {
                    return $agrupados[$record->tipo_inmueble_id]?->where('estado_departamento_id', $estado->id)->first()?->count ?? '0';
                })
                ->alignCenter();
        }

        return $table
            ->columns([
                TextColumn::make('tipo_inmueble')
                    ->label('Tipo de Inmueble')
                    ->getStateUsing(fn ($record) => TipoInmueble::find($record->tipo_inmueble_id)?->nombre ?? 'Sin tipo')
                    ->alignLeft()
                    ->weight('bold'),

                ...$estadoColumns,

                TextColumn::make('total')
                    ->label('Total')
                    ->getStateUsing(function ($record) use ($agrupados) {
                        return $agrupados[$record->tipo_inmueble_id]?->sum('count') ?? '0';
                    })
                    ->alignCenter()
                    ->weight('bold'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('edificio_id')
                    ->relationship('edificio', 'nombre')
                    ->label('Torre')
                    ->default(Edificio::first()?->id),
            ])
            ->actions([])
            ->bulkActions([])
            ->defaultSort('tipo_inmueble_id');
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
