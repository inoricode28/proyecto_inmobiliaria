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
    $estados = EstadoDepartamento::orderBy('nombre')->get();
    $tiposInmueble = TipoInmueble::orderBy('nombre')->get();

    // Precargar datos para optimización
    $departamentosPorEdificio = Departamento::with(['edificio', 'tipoInmueble', 'estadoDepartamento'])
        ->selectRaw('edificio_id, tipo_inmueble_id, estado_departamento_id, COUNT(*) as count')
        ->groupBy('edificio_id', 'tipo_inmueble_id', 'estado_departamento_id')
        ->get()
        ->groupBy(['edificio_id', 'tipo_inmueble_id']);

    // Columnas dinámicas para cada estado
    $estadoColumns = [];
    foreach ($estados as $estado) {
        $estadoColumns[] = TextColumn::make($estado->nombre)
            ->label($estado->nombre)
            ->getStateUsing(function ($record) use ($departamentosPorEdificio, $estado) {
                $count = $departamentosPorEdificio
                    ->get($record->edificio_id, collect())
                    ->get($record->tipo_inmueble_id, collect())
                    ->where('estado_departamento_id', $estado->id)
                    ->first()->count ?? 0;
                return $count == 0 ? '0' : (string)$count;
            })
            ->alignCenter();
    }

    return $table
        ->columns([
            TextColumn::make('tipoInmueble.nombre')
                ->label('Tipo de Inmueble')
                ->alignLeft()
                ->weight('bold'),

            ...$estadoColumns,

            TextColumn::make('total')
                ->label('Total')
                ->getStateUsing(function ($record) use ($departamentosPorEdificio) {
                    $total = $departamentosPorEdificio
                        ->get($record->edificio_id, collect())
                        ->get($record->tipo_inmueble_id, collect())
                        ->sum('count');
                    return $total == 0 ? '0' : (string)$total;
                })
                ->alignCenter()
                ->weight('bold'),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('edificio_id')
                ->relationship('edificio', 'nombre')
                ->default(request()->get('edificio_id') ?: Edificio::first()?->id)
                ->label('Torre'),
        ])
        ->actions([])
        ->bulkActions([])
        ->defaultSort('tipo_inmueble_id');
}

// Agrega este método para incluir los totales por columna
protected function getTableContent(): View
{
    return view('filament.resources.consulta-stock-resource.pages.custom-table', [
        'records' => $this->getTableRecords(),
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
