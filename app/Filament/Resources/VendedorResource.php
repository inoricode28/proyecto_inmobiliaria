<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VendedorResource\Pages;
use App\Filament\Resources\VendedorResource\RelationManagers;
use App\Models\Vendedor;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VendedorResource extends Resource
{
    protected static ?string $model = PanelSeguimientoResource::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Vendedor';
    protected static ?string $pluralModelLabel = 'Vendedor';
    protected static ?string $navigationGroup = 'Gestión Seguimiento';  
}
