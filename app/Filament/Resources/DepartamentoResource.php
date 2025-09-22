<?php

namespace App\Filament\Resources;

use Closure;
use App\Filament\Resources\DepartamentoResource\Pages;
use App\Models\Departamento;
use App\Models\Edificio;
use App\Models\TipoDepartamento;
use App\Models\TipoInmueble;
use App\Models\EstadoDepartamento;
use App\Models\TipoFinanciamiento;
use App\Models\Proyecto;
use App\Models\Vista;
use App\Models\Moneda;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Filters\SelectFilter;

class DepartamentoResource extends Resource
{
    protected static ?string $model = Departamento::class;
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?int $navigationSort = 4;
    protected static ?string $modelLabel = 'Departamento';
    protected static ?string $pluralModelLabel = 'Inmueble';

    protected static function getNavigationGroup(): ?string
    {
        return __('Management');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make()->schema([
                Select::make('proyecto_id')->label('Proyecto')->options(fn () => Proyecto::pluck('nombre', 'id'))->searchable()->columnSpan(1),
                Select::make('edificio_id')->label('Edificio')->required()->options(fn () => Edificio::pluck('nombre', 'id'))->searchable()->columnSpan(1),
                Select::make('tipo_inmueble_id')->label('Tipo de Inmueble')->options(fn () => TipoInmueble::pluck('nombre', 'id'))->searchable()->columnSpan(1),
                Select::make('tipo_departamento_id')->label('Tipo de Departamento')->options(fn () => TipoDepartamento::pluck('nombre', 'id'))->searchable()->required()->columnSpan(1),
                Select::make('estado_departamento_id')->label('Estado del Departamento')->options(fn () => EstadoDepartamento::pluck('nombre', 'id'))->searchable()->required()->columnSpan(1),
                Select::make('tipos_financiamiento_id')->label('Tipo de Financiamiento')->options(fn () => TipoFinanciamiento::pluck('nombre', 'id'))->searchable()->required()->columnSpan(1),
                TextInput::make('numero_inicial')->label('Número Inicial')->required()->maxLength(20)->regex('/^[A-Za-z0-9\-]+$/')->columnSpan(1),
                TextInput::make('numero_final')->label('Número Final')->maxLength(20)->regex('/^[A-Za-z0-9\-]+$/')->columnSpan(1),
                TextInput::make('ficha_indep')->label('Ficha Independiente')->maxLength(50)->regex('/^[A-Za-z0-9\-]+$/')->columnSpan(1),
                TextInput::make('num_departamento')->label('Número de Departamento')->maxLength(255)->columnSpan(1),
                TextInput::make('num_piso')->label('Número de Piso')->required()->numeric()->columnSpan(1),
                TextInput::make('num_dormitorios')->label('Dormitorios')->required()->numeric()->columnSpan(1),
                TextInput::make('num_bano')->label('Baños')->required()->numeric()->columnSpan(1),
                TextInput::make('num_certificado')->label('Número de Certificado')->maxLength(50)->regex('/^[A-Za-z0-9\-]+$/')->columnSpan(1),
                TextInput::make('codigo_bancario')->label('Código Bancario')->maxLength(50)->regex('/^[A-Za-z0-9\-]+$/')->columnSpan(1),
                TextInput::make('codigo_catastral')->label('Código Catastral')->maxLength(50)->regex('/^[A-Za-z0-9\-]+$/')->columnSpan(1),
                Toggle::make('bono_techo_propio')->label('Bono Techo Propio')->inline(false)->reactive()->columnSpan(1),
                TextInput::make('num_bono_tp')->label('Número Bono TP')->maxLength(50)->regex('/^[A-Za-z0-9\-]+$/')->columnSpan(1)->visible(fn (Closure $get) => $get('bono_techo_propio')),
                Select::make('moneda_id')->label('Moneda')->options(fn () => Moneda::pluck('nombre', 'id'))->searchable()->required()->columnSpan(1),
                TextInput::make('precio')->label('Precio')->required()->numeric()->columnSpan(1),
                TextInput::make('Precio_lista')->label('Precio de Lista')->numeric()->columnSpan(1),
                TextInput::make('Precio_venta')->label('Precio de Venta')->numeric()->columnSpan(1),
                TextInput::make('descuento')->label('Descuento (%)')->numeric()->suffix('%')->columnSpan(1),
                TextInput::make('predio_m2')->label('Área Predio (m²)')->numeric()->suffix('m²')->columnSpan(1),
                TextInput::make('terreno')->label('Terreno (m²)')->numeric()->suffix('m²')->columnSpan(1),
                TextInput::make('techada')->label('Techada (m²)')->numeric()->suffix('m²')->columnSpan(1),
                TextInput::make('construida')->label('Construida (m²)')->numeric()->suffix('m²')->columnSpan(1),
                TextInput::make('terraza')->label('Terraza (m²)')->numeric()->suffix('m²')->columnSpan(1),
                TextInput::make('jardin')->label('Jardín (m²)')->numeric()->suffix('m²')->columnSpan(1),
                Select::make('vista_id')->label('Vista')->options(fn () => Vista::pluck('nombre', 'id'))->searchable()->columnSpan(1),
                TextInput::make('orden')->label('Orden')->numeric()->columnSpan(1),
                Textarea::make('direccion')->label('Dirección')->required()->maxLength(500)->columnSpan(2),
                TextInput::make('frente')->label('Frente (m)')->numeric()->suffix('m')->columnSpan(1),
                TextInput::make('derecha')->label('Derecha (m)')->numeric()->suffix('m')->columnSpan(1),
                TextInput::make('izquierda')->label('Izquierda (m)')->numeric()->suffix('m')->columnSpan(1),
                TextInput::make('fondo')->label('Fondo (m)')->numeric()->suffix('m')->columnSpan(1),
                Textarea::make('observaciones')->label('Observaciones')->maxLength(1000)->columnSpan(2),
                Toggle::make('vendible')->label('¿Vendible?')->columnSpan(1),
                Select::make('estado_id')->label('Estado')->options([1 => 'Activo', 2 => 'Inactivo'])->required()->default(1)->columnSpan(1),
            ])->columns(2)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('numero_inicial')->label('Número')->sortable()->searchable(),
                TextColumn::make('num_departamento')->label('Núm. Departamento')->sortable()->searchable(),
                TextColumn::make('edificio.nombre')->label('Edificio')->sortable()->searchable(),
                TextColumn::make('num_piso')->label('Piso')->sortable(),
                TextColumn::make('tipoDepartamento.nombre')->label('Tipo')->sortable(),
                TextColumn::make('estadoDepartamento.nombre')->label('Estado Dep.')->sortable(),
                TextColumn::make('moneda.nombre')->label('Moneda')->sortable(),
                TextColumn::make('precio')->label('Precio')->formatStateUsing(fn ($state) => '$' . number_format($state, 2, '.', ','))->sortable(),
                TextColumn::make('predio_m2')->suffix(' m²')->label('Área Predio')->sortable(),
                TextColumn::make('tipoFinanciamiento.nombre')->label('Financiamiento')->sortable(),
                BooleanColumn::make('bono_techo_propio')->label('Bono TP')->sortable(),
                BooleanColumn::make('vendible')->label('Vendible')->sortable(),
                BooleanColumn::make('estado_id')
                    ->label('Estado')
                    ->getStateUsing(fn ($record): bool => $record->estado_id === 1)
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('edificio_id')->label('Edificio')->options(fn () => Edificio::pluck('nombre', 'id')),
                SelectFilter::make('tipo_departamento_id')->label('Tipo')->options(fn () => TipoDepartamento::pluck('nombre', 'id')),
                SelectFilter::make('estado_departamento_id')->label('Estado Dep.')->options(fn () => EstadoDepartamento::pluck('nombre', 'id')),
                SelectFilter::make('estado_id')->label('Activo/Inactivo')->options([1 => 'Activo', 2 => 'Inactivo']),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->icon('heroicon-o-pencil'),
                Tables\Actions\DeleteAction::make()->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
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
        return ['numero_inicial', 'num_departamento', 'edificio.nombre', 'codigo_bancario'];
    }
}
