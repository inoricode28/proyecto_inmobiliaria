<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FotoDepartamentoResource\Pages;
use App\Filament\Resources\FotoDepartamentoResource\RelationManagers;
use App\Models\FotoDepartamento;
use App\Models\Proyecto;
use App\Models\Edificio;
use App\Models\Departamento;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Card;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage; // <-- Esta es la importación que falta


class FotoDepartamentoResource extends Resource
{
    protected static ?string $model = FotoDepartamento::class;

    protected static ?string $navigationIcon = 'heroicon-o-camera';
    
    protected static ?string $modelLabel = 'Foto de Departamento';
    
    protected static ?string $pluralModelLabel = 'Fotos de Departamentos';
    
    protected static ?string $navigationGroup = 'Gestión';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Select::make('proyecto_id')
                        ->label('Proyecto')
                        ->options(Proyecto::all()->pluck('nombre', 'id'))
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function (callable $set) {
                            $set('edificio_id', null);
                            $set('departamento_id', null);
                        })
                        ->searchable(),
                        
                    Select::make('edificio_id')
                        ->label('Edificio')
                        ->options(function (callable $get) {
                            $proyectoId = $get('proyecto_id');
                            return $proyectoId ? Edificio::where('proyecto_id', $proyectoId)->pluck('nombre', 'id') : [];
                        })
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function (callable $set) {
                            $set('departamento_id', null);
                        })
                        ->searchable(),
                        
                    Select::make('departamento_id')
                        ->label('Departamento')
                        ->options(function (callable $get) {
                            $edificioId = $get('edificio_id');
                            if (!$edificioId) {
                                return [];
                            }
                            
                            return Departamento::where('edificio_id', $edificioId)
                                ->get()
                                ->mapWithKeys(function ($item) {
                                    return [$item->id => "Piso {$item->piso} - Depto. {$item->numero}"];
                                })
                                ->toArray();
                        })
                        ->required()
                        ->searchable(),
                        
                    FileUpload::make('imagen')
                        ->label('Imagen del Departamento')
                        ->directory('departamentos')
                        ->image()
                        ->required()
                        ->maxSize(2048)
                        ->enableOpen()
                        ->enableDownload()
                        ->preserveFilenames()
                        ->columnSpanFull(),
                ])->columns(1)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('imagen')
                    ->label('Foto')
                    ->size(80)
                    ->square(),
                    
                TextColumn::make('proyecto.nombre')
                    ->searchable()
                    ->sortable()
                    ->label('Proyecto'),
                    
                TextColumn::make('edificio.nombre')
                    ->searchable()
                    ->sortable()
                    ->label('Edificio'),
                    
                TextColumn::make('departamento.numero')
                    ->searchable()
                    ->sortable()
                    ->label('Departamento N°'),
                    
                TextColumn::make('departamento.piso')
                    ->sortable()
                    ->label('Piso'),
                    
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Fecha de creación'),
            ])
            ->filters([
                SelectFilter::make('proyecto_id')
                    ->label('Proyecto')
                    ->options(Proyecto::all()->pluck('nombre', 'id'))
                    ->searchable(),
                    
                SelectFilter::make('edificio_id')
                    ->label('Edificio')
                    ->options(Edificio::all()->pluck('nombre', 'id'))
                    ->searchable(),
                    
                SelectFilter::make('departamento_id')
                    ->label('Departamento')
                    ->options(function () {
                        return Departamento::all()
                            ->mapWithKeys(function ($item) {
                                return [$item->id => "Piso {$item->piso} - Depto. {$item->numero}"];
                            })
                            ->toArray();
                    })
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye'),
                    
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil'),
                    
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->before(function ($record) {
                        Storage::disk('public')->delete($record->imagen);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->before(function ($records) {
                        foreach ($records as $record) {
                            Storage::disk('public')->delete($record->imagen);
                        }
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers pueden agregarse aquí
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFotoDepartamentos::route('/'),
            'create' => Pages\CreateFotoDepartamento::route('/create'),
            'view' => Pages\ViewFotoDepartamento::route('/{record}'),
            'edit' => Pages\EditFotoDepartamento::route('/{record}/edit'),
        ];
    }
    
    public static function getGloballySearchableAttributes(): array
    {
        return ['proyecto.nombre', 'edificio.nombre', 'departamento.numero'];
    }
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['proyecto', 'edificio', 'departamento'])
            ->latest();
    }
}