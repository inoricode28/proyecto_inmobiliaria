<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VendedorResource\Pages;
use App\Filament\Resources\VendedorResource\RelationManagers;
use App\Models\Vendedor;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class VendedorResource extends Resource
{
    protected static ?string $model = Vendedor::class;
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $modelLabel = 'Vendedor';
    protected static ?string $pluralModelLabel = 'Vendedores';
    protected static ?string $navigationGroup = 'Gestión Seguimiento';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Select::make('user_id')
                        ->relationship('user', 'name')
                        ->required()
                        ->label('Usuario asociado')
                        ->searchable()
                        ->preload(),

                    Select::make('tipo_documento_id')
                        ->relationship('tipoDocumento', 'nombre')
                        ->required()
                        ->label('Tipo de Documento')
                        ->searchable()
                        ->preload(),

                    TextInput::make('numero_documento')
                        ->required()
                        ->maxLength(20)
                        ->label('Número de Documento'),

                    TextInput::make('nombre')
                        ->required()
                        ->maxLength(100)
                        ->label('Nombre Completo'),

                    TextInput::make('telefono')
                        ->required()
                        ->maxLength(20)
                        ->label('Teléfono'),

                    TextInput::make('email')
                        ->required()
                        ->email()
                        ->maxLength(100)
                        ->label('Correo Electrónico'),

                    Select::make('estado_id')
                        ->relationship('estado', 'id') // Usamos id ya que no hay campo nombre
                        ->required()
                        ->label('Estado')
                        ->options([
                            1 => 'Activo',
                            2 => 'Inactivo'
                        ])
                        ->searchable(),

                    DatePicker::make('fecha_ingreso')
                        ->required()
                        ->label('Fecha de Ingreso')
                        ->displayFormat('d/m/Y'),

                    DatePicker::make('fecha_egreso')
                        ->label('Fecha de Egreso')
                        ->displayFormat('d/m/Y'),

                    Select::make('proyecto_id')
                        ->relationship('proyecto', 'nombre')
                        ->label('Proyecto')
                        ->searchable()
                        ->preload(),

                    TextInput::make('comision')
                        ->numeric()
                        ->mask(fn (TextInput\Mask $mask) => $mask->pattern('00.00'))
                        ->label('Comisión (%)'),

                    TextInput::make('perfil')
                        ->maxLength(255)
                        ->label('Perfil'),
                        Forms\Components\Hidden::make('created_by')
                    ->default(auth()->id())
                    ->disabled(),

                Forms\Components\Hidden::make('updated_by')
                    ->default(auth()->id())
                    ->disabled(),
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

                TextColumn::make('numero_documento')
                    ->searchable()
                    ->label('Documento'),

                TextColumn::make('telefono')
                    ->searchable(),

                TextColumn::make('email')
                    ->searchable(),

                TextColumn::make('estado.activo')
                    ->label('Estado')
                    ->formatStateUsing(fn ($state) => $state ? 'Activo' : 'Inactivo')
                    ->sortable(),

                TextColumn::make('fecha_ingreso')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('comision')
                    ->label('Comisión (%)')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state, 2).'%'),

                TextColumn::make('proyecto.nombre')
                    ->label('Proyecto')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado_id')
                    ->label('Estado')
                    ->options([
                        1 => 'Activo',
                        2 => 'Inactivo'
                    ]),

                Tables\Filters\SelectFilter::make('proyecto_id')
                    ->label('Proyecto')
                    ->relationship('proyecto', 'nombre')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil'),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->before(function (Vendedor $record, Tables\Actions\DeleteAction $action) {
                        if ($record->ventas()->count() > 0) {
                            Notification::make()
                                ->danger()
                                ->title('No se puede eliminar')
                                ->body('Este vendedor tiene ventas asociadas')
                                ->persistent()
                                ->send();
                            $action->cancel();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVendedores::route('/'),
            'create' => Pages\CreateVendedor::route('/create'),
            'edit' => Pages\EditVendedor::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['nombre', 'numero_documento', 'email', 'telefono'];
    }
}
