<?php

namespace App\Filament\Resources;

use App\Models\Tarea;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use App\Models\Proyecto;
use App\Models\User;
use App\Models\ComoSeEntero;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\DatePicker;
use App\Filament\Resources\PanelSeguimientoResource\Pages;
use App\Filament\Resources\PanelSeguimientoResource\Widgets\SeguimientoFilters;

class PanelSeguimientoResource extends Resource
{
    protected static ?string $model = Tarea::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-check';
    protected static ?string $navigationLabel = 'Panel de Seguimiento';
    protected static ?string $modelLabel = 'Seguimiento';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Gestión Seguimiento';
    protected static ?string $routePath = 'seguimiento';
    protected static ?string $modelResourceName = 'seguimiento';

    public static function form(Form $form): Form
    {
        // Este formulario solo se usará si hay acciones de creación/edición
        return $form->schema([/*...*/]);
    }

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            \Filament\Tables\Columns\TextColumn::make('prospecto.nombres')
                ->label('Nombres')
                ->formatStateUsing(function ($record) {
                    return $record->prospecto->nombres . ' ' . $record->prospecto->ape_paterno;
                })
                ->searchable(),

            \Filament\Tables\Columns\TextColumn::make('prospecto.celular')
                ->label('Teléfono')
                ->searchable(),

            \Filament\Tables\Columns\TextColumn::make('prospecto.proyecto.nombre')
                ->label('Proyecto'),

            \Filament\Tables\Columns\TextColumn::make('prospecto.comoSeEntero.nombre')
                ->label('Cómo se enteró'),

            \Filament\Tables\Columns\TextColumn::make('prospecto.fecha_registro')
                ->label('Fec. Registro')
                ->date('d/m/Y'),

            \Filament\Tables\Columns\TextColumn::make('fecha_contacto')
                ->label('Fec. Últ. Contacto')
                ->dateTime('d/m/Y H:i'),

            \Filament\Tables\Columns\TextColumn::make('fecha_vencimiento')
                ->label('Fec. Tarea')
                ->dateTime('d/m/Y H:i'),


            \Filament\Tables\Columns\TextColumn::make('usuarioAsignado.name')
                ->label('Responsable'),
        ])
        ->filters([
            // Filtros adicionales si los necesitas
        ]);
}

    // En PanelSeguimientoResource.php
public static function getWidgets(): array
{
    return [
        SeguimientoFilters::class,
    ];
}

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPanelSeguimientos::route('/'),
        ];
    }
}
