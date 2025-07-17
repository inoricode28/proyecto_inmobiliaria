<?php

namespace App\Filament\Resources;
use Filament\Forms\Components\Field;

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
use Closure;





class GestionSeguimientoResource extends Resource
{
    protected static ?string $model = Prospecto::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Búsqueda de Prospectos';
    protected static ?string $modelLabel = 'Prospecto';
    protected static ?int $navigationSort = 1;

    protected static ?string $pluralModelLabel = 'Búsqueda de Prospectos';
    protected static ?string $navigationGroup = 'Gestión Seguimiento';


public static function form(Form $form): Form
{
    $tipoDni = TipoDocumento::where('nombre', 'DNI')->value('id');
    $tipoRuc = TipoDocumento::where('nombre', 'RUC')->value('id');
    $tipoIndocumentado = TipoDocumento::where('nombre', 'INDOCUMENTADO')->value('id');

    return $form->schema([
        Grid::make(3)->schema([
            DatePicker::make('prospecto_fecha_registro')
                ->label('Fecha Registro')
                ->displayFormat('d/m/Y')
                ->default(now()->format('Y-m-d'))
                ->required()
                ->columnSpan(1),

            Select::make('tipo_documento_id')
                ->label('Tipo de Documento')
                ->options(TipoDocumento::all()->pluck('nombre', 'id'))
                ->default($tipoIndocumentado) // Valor por defecto
                ->required()
                ->reactive()
                ->afterStateUpdated(function (Closure $set, $state) use ($tipoIndocumentado) {
                    if ($state == $tipoIndocumentado) {
                        $set('numero_documento', null); // Limpiar el campo
                    }
                })
                ->columnSpan(1),


            TextInput::make('numero_documento')
                ->label('N° Documento')

                ->disabled(fn (Closure $get) => $get('tipo_documento_id') == $tipoIndocumentado)
                ->placeholder(fn (Closure $get) => $get('tipo_documento_id') == $tipoIndocumentado ? 'No aplica' : '')
                ->extraAttributes(fn (Closure $get) => $get('tipo_documento_id') == $tipoIndocumentado
                    ? [
                        'class' => 'bg-gray-800',
                    ] : []
                )
                ->columnSpan(1),

        ]),

        Grid::make(3)->schema([
            TextInput::make('razon_social')
                ->label('Razón Social')
                ->required()
                ->visible(fn (Closure $get) => $get('tipo_documento_id') == $tipoRuc)
                ->columnSpanFull(),

            TextInput::make('nombres')
                ->label('Nombres')
                ->required()
                ->visible(fn (Closure $get) => in_array($get('tipo_documento_id'), [$tipoDni, $tipoIndocumentado]))
                ->columnSpan(1),

            TextInput::make('ape_paterno')
                ->label('Ape. Paterno')
                ->required()
                ->visible(fn (Closure $get) => in_array($get('tipo_documento_id'), [$tipoDni, $tipoIndocumentado]))
                ->columnSpan(1),

            TextInput::make('ape_materno')
                ->label('Ape. Materno')
                ->visible(fn (Closure $get) => in_array($get('tipo_documento_id'), [$tipoDni, $tipoIndocumentado]))
                ->columnSpan(1),
        ]),

        Grid::make(3)->schema([
            TextInput::make('celular')
                ->label('Celular')
                ->required()
                ->columnSpan(1),
/*
             Select::make('tipo_gestion_id')
                ->label('Tipo de Gestión')
                ->options(TipoGestion::all()->pluck('nombre', 'id'))
                ->default(1) // Establece NO GESTIONADO (ID 1) como valor por defecto
                ->required(),
*/
            TextInput::make('correo_electronico')
                ->label('Correo Electrónico')
                ->email()
                ->columnSpan(1),
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

            Select::make('prospecto_forma_contacto_id')
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




            // Sección de Tarea
            Section::make('TAREA')
                ->schema([
                    Card::make()
                        ->schema([

                             Grid::make(1)->schema([
                    Field::make('tarea_forma_contacto_id')
                        ->label('Forma de Contacto')
                        ->required()
                        ->view('filament.resources.gestion-seguimiento-resource.forma-contacto-icons'),

                    Field::make('nivel_interes_id')
                        ->label('Nivel de Interés')
                        ->required()
                        ->view('filament.resources.gestion-seguimiento-resource.nivel-interes-buttons'),
                ]),

                            Grid::make(3)->schema([



                                Grid::make(3)->schema([
                                    Select::make('usuario_asignado_id')
                                        ->label('Usuario Asignado')
                                        ->options(User::all()->pluck('name', 'id'))
                                        ->placeholder('-Seleccione-')
                                        ->required()
                                        ->columnSpan(1),

                                    DatePicker::make('tarea_fecha_realizar')
                                        ->label('Fecha Realizar')
                                        ->displayFormat('d/m/Y')
                                        ->default(now()->format('Y-m-d'))
                                        ->required()
                                        ->columnSpan(1),

                                    TextInput::make('hora_seguimiento')
                                        ->label('Hora')
                                        ->type('time')
                                        ->default('10:00')
                                        ->required()
                                        ->columnSpan(1),

                                    Textarea::make('nota')
                                        ->label('Nota Adicional')
                                        ->placeholder('Ingrese observaciones del seguimiento')
                                        ->columnSpanFull(),
                                ]),
                            ]),
                        ])
                        ->columnSpan(3),
                ]),
        ]);
    }


    public static function table(Table $table): Table
    {
        return $table
                ->columns([
                    TextColumn::make('nombre_completo')
                    ->label('Nombre Completo')
                    ->getStateUsing(function ($record) {
                        // Si tiene razón social y NO tiene nombres personales
                        if (empty($record->nombres) && empty($record->ape_paterno) && empty($record->ape_materno)) {
                            return $record->razon_social ?? '-';
                        }

                        // Armar nombre completo
                        $nombreCompleto = trim("{$record->nombres} {$record->ape_paterno} {$record->ape_materno}");
                        return $nombreCompleto;
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->where('nombres', 'like', "%{$search}%")
                            ->orWhere('ape_paterno', 'like', "%{$search}%")
                            ->orWhere('ape_materno', 'like', "%{$search}%")
                            ->orWhere('razon_social', 'like', "%{$search}%");
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->orderByRaw("COALESCE(ape_paterno, razon_social) {$direction}")
                            ->orderBy('ape_materno', $direction)
                            ->orderBy('nombres', $direction);
                    })

                    ->sortable(),

                TextColumn::make('numero_documento')
                    ->label('No Documento')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('celular')
                    ->label('Celular')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('correo_electronico')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),


                TextColumn::make('fecha_registro')
                    ->label('Fecha Registro')
                    ->date('d/m/Y')
                    ->sortable(),

                    /*
                BooleanColumn::make('estado_id')
                    ->label('Estado')
                    ->getStateUsing(fn ($record): bool => $record->estado_id === 1)
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
                    */

                TextColumn::make('tareaAsignada.usuarioAsignado.name')
                    ->label('Responsable')
                    ->sortable()
                    ->searchable(),




            ])
            ->filters([
                SelectFilter::make('proyecto_id')
                    ->label('Proyecto')
                    ->relationship('proyecto', 'nombre'),

/*
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
                    }),*/
            ])
            ->actions([
               // Tables\Actions\EditAction::make(),
               // Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //Tables\Actions\DeleteBulkAction::make(),
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
