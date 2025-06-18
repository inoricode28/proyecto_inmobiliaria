<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmpresaResource\Pages;
use App\Filament\Resources\EmpresaResource\RelationManagers;
use App\Models\Empresa;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;

class EmpresaResource extends Resource
{
    protected static ?string $model = Empresa::class;

    protected static ?string $navigationIcon = 'heroicon-o-office-building';
    
    protected static ?string $modelLabel = 'Empresa';
    
    protected static ?string $pluralModelLabel = 'Empresas';
    
     protected static function getNavigationGroup(): ?string
    {
        return __('Management');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    TextInput::make('nombre')
                        ->required()
                        ->maxLength(100)
                        ->label('Nombre de la Empresa'),
                        
                    TextInput::make('ruc')
                        ->required()
                        ->length(11)
                        ->unique(ignoreRecord: true)
                        ->label('RUC')
                        ->mask(fn (TextInput\Mask $mask) => $mask->pattern('00000000000')),
                        
                    TextInput::make('direccion')
                        ->required()
                        ->maxLength(255)
                        ->label('Dirección'),
                        
                    TextInput::make('telefono')
                        ->required()
                        ->maxLength(20)
                        ->label('Teléfono'),
                        
                    TextInput::make('email')
                        ->required()
                        ->email()
                        ->maxLength(100)
                        ->label('Correo Electrónico'),
                        
                    TextInput::make('representante_legal')
                        ->required()
                        ->maxLength(100)
                        ->label('Representante Legal'),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('ruc')
                    ->searchable(),
                    
                TextColumn::make('telefono')
                    ->searchable(),
                    
                TextColumn::make('email')
                    ->searchable(),
                    
                TextColumn::make('proyectos_count')
                    ->counts('proyectos')
                    ->label('Proyectos')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil'),
                    
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->before(function (Empresa $record, Tables\Actions\DeleteAction $action) {
                        if ($record->proyectos()->count() > 0) {
                            Notification::make()
                                ->danger()
                                ->title('No se puede eliminar')
                                ->body('Esta empresa tiene proyectos asociados')
                                ->persistent()
                                ->send();
                            $action->cancel();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->before(function ($action, $records) {
                        foreach ($records as $record) {
                            if ($record->proyectos()->count() > 0) {
                                Notification::make()
                                    ->danger()
                                    ->title('No se puede eliminar')
                                    ->body("La empresa {$record->nombre} tiene proyectos asociados")
                                    ->persistent()
                                    ->send();
                                $action->cancel();
                                break;
                            }
                        }
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
           // RelationManagers\ProyectosRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmpresas::route('/'),
            'create' => Pages\CreateEmpresa::route('/create'),
            'edit' => Pages\EditEmpresa::route('/{record}/edit'),
        ];
    }
    
    public static function getGloballySearchableAttributes(): array
    {
        return ['nombre', 'ruc', 'email', 'representante_legal'];
    }
}