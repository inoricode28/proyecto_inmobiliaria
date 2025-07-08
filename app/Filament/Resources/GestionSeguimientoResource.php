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
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\BooleanColumn;
use App\Models\TipoDocumento;
use App\Models\TipoInmueble;
use App\Models\TipoGestion;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Hidden;
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
use Filament\Forms\Components\View;
use Filament\Tables\Headers\ActionsHeader;
use Filament\Forms\Components\Text;
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
        Grid::make(3)->schema([
            DatePicker::make('fecha_registro')
                ->label('Fecha Registro')
                -> displayFormat('d/m/Y')
                ->default(now()->format('Y-m-d')) // Explicit format for database
                ->default(now())
                ->required()
                ->columnSpan(1),

            Select::make('tipo_documento_id')
                ->label('Tipo de Documento')
                ->options(TipoDocumento::all()->pluck('nombre', 'id'))
                ->required()
                ->columnSpan(1),

            TextInput::make('numero_documento')
                ->label('N° Documento')
                ->required()
                ->columnSpan(1),
        ]),

        Grid::make(3)->schema([
            TextInput::make('nombres')
                ->label('Nombres')
                ->required()
                ->columnSpan(1),

            TextInput::make('ape_paterno')
                ->label('Ape. Paterno')
                ->required()
                ->columnSpan(1),

            TextInput::make('ape_materno')
                ->label('Ape. Materno')
                ->columnSpan(1),
        ]),

       Grid::make(3)->schema([
        TextInput::make('celular')
            ->label('Celular')
            ->required()
            ->columnSpan(1),

        Select::make('tipo_gestion_id')
            ->label('Tipo Gestión')
            ->options(TipoGestion::all()->pluck('nombre', 'id'))
            ->required()
            ->columnSpan(1),

        TextInput::make('correo_electronico')
            ->label('Correo Electrónico')
            ->email()
            ->columnSpan(1), // Ocupará toda la siguiente fila
    ]),

        Grid::make(4)->schema([
            Select::make('proyecto_id')
                ->label('Proyecto')
                ->options(Proyecto::all()->pluck('nombre', 'id'))
                ->required()
                ->columnSpan(1),

            Select::make('tipo_inmueble_id')
                ->label('Tipo Inmueble')
                ->options(TipoInmueble::all()->pluck('nombre', 'id'))
                ->required()
                ->columnSpan(1),

            Select::make('forma_contacto_id')
                ->label('Forma de Contacto')
                ->options(FormaContacto::all()->pluck('nombre', 'id'))
                ->required()
                ->columnSpan(1),

            Select::make('como_se_entero_id')
                ->label('Como se Enteró')
                ->options(ComoSeEntero::all()->pluck('nombre', 'id'))
                ->required()
                ->columnSpan(1),
        ]),





        Card::make()
        ->schema([
            Placeholder::make('')
                ->content('TAREA')
                ->extraAttributes([
                    'class' => 'text-xl font-bold uppercase px-4 py-3 bg-gray-100 border-b border-gray-200 w-full'
                ]),

        Grid::make(3)->schema([
            Card::make()
                ->schema([
                    // En tu schema:
                    Hidden::make('forma_contacto_id')
                        ->required()
                        ->rules(['required']),

                    Hidden::make('nivel_interes_id')
                        ->required()
                        ->rules(['required']),

                    // Luego tus vistas normales sin modificar
                    View::make('filament.resources.gestion-seguimiento-resource.forma-contacto-icons')
                        ->label('Forma de Contacto')
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $set('forma_contacto_id', $state);
                        }),

                    View::make('filament.resources.gestion-seguimiento-resource.nivel-interes-buttons')
                        ->label('Nivel de interés')
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $set('nivel_interes_id', $state);
                        }),

                    Grid::make(3)->schema([
                        Select::make('usuario_asignado_id')
                            ->label('Usuario Asignado')
                            ->options(User::all()->pluck('name', 'id'))
                            ->placeholder('-Seleccione-')
                            ->required()
                            ->columnSpan(1),

                        DatePicker::make('fecha_registro')
                        ->label('Fecha Registro')
                        ->displayFormat('d/m/Y')
                        ->default(now()->format('Y-m-d')) // Explicit format for database
                        ->required()
                        ->columnSpan(1),

                        TextInput::make('hora_seguimiento')
                            ->label('Hora')
                            ->type('time')
                            ->default('10:00')
                            ->required()
                            ->columnSpan(1),

                        Textarea::make('nota')
                            ->label('Nota')
                            ->placeholder('Ingrese observaciones del seguimiento')
                            ->columnSpanFull(),
                    ]),
                ])
                ->columnSpan(3),
        ]),
    ])
    ->columnSpanFull(),


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

                BooleanColumn::make('estado_id')
                    ->label('Estado')
                    ->getStateUsing(fn ($record): bool => $record->estado_id === 1)
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

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
