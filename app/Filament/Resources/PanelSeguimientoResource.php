<?php

namespace App\Filament\Resources;


use App\Filament\Resources\PanelSeguimientoResource\Pages;
use App\Models\PanelSeguimiento;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PanelSeguimientoResource extends Resource
{
    protected static ?string $model = PanelSeguimientoResource::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Panel Seguimiento';
    protected static ?string $pluralModelLabel = 'Panel Seguimiento';
    protected static ?string $navigationGroup = 'Gestión Seguimiento';


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPanelSeguimientos::route('/'),
            'edit' => Pages\EditPanelSeguimiento::route('/{record}/edit'),
        ];
    }
}
