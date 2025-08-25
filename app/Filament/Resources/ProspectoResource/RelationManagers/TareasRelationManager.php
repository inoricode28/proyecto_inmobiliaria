<?php
namespace App\Filament\Resources\ProspectoResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class TareasRelationManager extends RelationManager
{
    protected static string $relationship = 'tareas';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('formaContacto.nombre')->label('Contacto'),
                Tables\Columns\TextColumn::make('fecha_realizar')->label('Fecha')->date('d/m/Y'),
                Tables\Columns\TextColumn::make('hora')->label('Hora'),
                Tables\Columns\TextColumn::make('nivelInteres.nombre')->label('Interés'),
                Tables\Columns\TextColumn::make('usuarioAsignado.name')->label('Responsable'),
                Tables\Columns\TextColumn::make('respuesta')->label('Respuesta')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('nota')->label('Comentario')->wrap(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([])      // solo lectura
            ->bulkActions([]); // solo lectura
    }
}
