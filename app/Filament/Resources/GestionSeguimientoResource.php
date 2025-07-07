<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GestionSeguimientoResource\Pages;
use App\Filament\Resources\GestionSeguimientoResource\RelationManagers;
use App\Models\Prospecto;
use App\Models\Proyecto;
use App\Models\User;
use App\Models\FormaContacto;
use App\Models\ComoSeEntero;
use App\Models\NivelInteres;
use Filament\Forms\Components\Button;
use Filament\Forms;

use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Actions\Action;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class GestionSeguimientoResource extends Resource
{
    protected static ?string $model = Prospecto::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Búsqueda de Prospectos';
    protected static ?string $modelLabel = 'Prospecto';
    protected static ?string $pluralModelLabel = 'Búsqueda de Prospectos';
    protected static ?string $navigationGroup = 'Gestión Seguimiento';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Filtros de Búsqueda')
                ->schema([
                    Grid::make(4)->schema([
                        Select::make('usuario_asignado')
                            ->label('Usuario Asignado')
                            ->options(User::all()->pluck('name', 'id'))
                            ->searchable()
                            ->default('all')
                            ->placeholder('Todos'),
                            
                        TextInput::make('nombres')
                            ->label('Nombres')
                            ->placeholder('Ingresar Nombres'),
                            
                        TextInput::make('numero_documento')
                            ->label('No Documento')
                            ->placeholder('Ingresar Nº documento'),
                            
                        Select::make('estado')
                            ->label('Estado')
                            ->options([
                                'ACTIVO' => 'Activo',
                                'INACTIVO' => 'Inactivo',
                                'POTENCIAL' => 'Potencial'
                            ])
                            ->default('ACTIVO'),
                    ]),
                    
                    Grid::make(4)->schema([
                        DatePicker::make('fecha_inicio')
                            ->label('Fecha Registro Inicio')
                            ->displayFormat('d/m/Y')
                            ->default(now()->subMonths(3)),
                            
                        DatePicker::make('fecha_fin')
                            ->label('Fecha Registro Fin')
                            ->displayFormat('d/m/Y')
                            ->default(now()),
                            
                        Select::make('tipo_fecha')
                            ->label('Tipo Fecha')
                            ->options([
                                'registro' => 'Registro',
                                'contacto' => 'Último Contacto',
                                'operacion' => 'Última Operación'
                            ])
                            ->default('registro'),
                            
                        Select::make('proyecto')
                            ->label('Proyecto')
                            ->options(Proyecto::all()->pluck('nombre', 'id'))
                            ->searchable()
                            ->placeholder('Todos'),
                    ]),
                    
                    Grid::make(4)->schema([
                        Select::make('nivel_interes')
                            ->label('Nivel de Interés')
                            ->options(NivelInteres::all()->pluck('nombre', 'id'))
                            ->searchable()
                            ->placeholder('Todos'),
                            
                        Select::make('forma_contacto')
                            ->label('Forma de Contacto')
                            ->options(FormaContacto::all()->pluck('nombre', 'id'))
                            ->searchable()
                            ->placeholder('Todos'),
                            
                        Select::make('como_se_entero_id')
                            ->label('Como se Enteró')
                            ->options(ComoSeEntero::all()->pluck('nombre', 'id'))
                            ->searchable()
                            ->placeholder('Todos'),
                            
                        Grid::make(2)->schema([
                            Checkbox::make('eventos_vencidos')
                                ->label('Eventos Vencidos'),
                                
                            Checkbox::make('con_score')
                                ->label('Con Score'),
                                
                            Checkbox::make('prospecto_nuevo')
                                ->label('Prospecto Nuevo'),
                                
                            Action::make('buscar')
                                ->label('Buscar')
                                ->action(fn () => null)
                                ->button()
                                ->color('primary'),
                        ])->columns(4),
                    ]),
                ])
                ->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {       
        return $table
            ->columns([
                TextColumn::make('nombres')
                    ->label('Nombres')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('numero_documento')
                    ->label('No Documento')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('estado')
                    ->label('Estado')
                    ->searchable(),
                    
                TextColumn::make('fecha_registro')
                    ->label('Fecha Registro')
                    ->date('d/m/Y')
                    ->sortable(),
                    
                TextColumn::make('proyecto.nombre')
                    ->label('Proyecto')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('proyecto_id')
                    ->label('Proyecto')
                    ->relationship('proyecto', 'nombre'),
                    
                SelectFilter::make('estado')
                    ->options([
                        'ACTIVO' => 'Activo',
                        'INACTIVO' => 'Inactivo',
                        'POTENCIAL' => 'Potencial'
                    ]),
                    
                Filter::make('fecha_registro')
                    ->form([
                        DatePicker::make('fecha_inicio')
                            ->label('Desde')
                            ->displayFormat('d/m/Y'),
                        DatePicker::make('fecha_fin')
                            ->label('Hasta')
                            ->displayFormat('d/m/Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['fecha_inicio'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_registro', '>=', $date),
                            )
                            ->when(
                                $data['fecha_fin'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_registro', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGestionSeguimientos::route('/'),
            'create' => Pages\CreateGestionSeguimiento::route('/create'),
            'edit' => Pages\EditGestionSeguimiento::route('/{record}/edit'),
            'view' => Pages\ViewGestionSeguimiento::route('/{record}'),
        ];
    }
}