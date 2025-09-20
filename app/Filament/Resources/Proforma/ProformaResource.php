<?php

namespace App\Filament\Resources\Proforma;

use App\Models\Proforma;
use Filament\Forms;
//use Filament\Forms\Form;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\{Grid, TextInput, Select, DatePicker, Textarea, FileUpload};
use Filament\Forms\Components\Hidden;
use App\Filament\Resources\Proforma\ProformaResource\Pages;
use Barryvdh\DomPDF\Facade\Pdf;

class ProformaResource extends Resource
{
    protected static ?string $model = Proforma::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Proformas';
    protected static ?string $navigationGroup = 'Ventas';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('Proforma')
                -> columnSpan('full')
                ->tabs([
                    Tab::make('Cliente')->schema([
                        Grid::make(3)->schema([

                            TextInput::make('correo')
                                ->label('Correo Electrónico')
                                ->disabled(), // Campo deshabilitado ya que se precarga desde el action

                            Select::make('tipo_documento_id')
                                ->label('Tipo de Documento')
                                ->relationship('tipoDocumento', 'nombre'),
                              //  ->required(),

                            TextInput::make('numero_documento')
                                ->label('N° Documento'),
                                // ->reactive()
                                // ->afterStateUpdated(function (callable $set, $state) {
                                //     if (!empty($state)) {
                                //         $prospecto = \App\Models\Prospecto::where('numero_documento', $state)->first();
                                //
                                //         if ($prospecto) {
                                //             $set('prospecto_id', $prospecto->id);
                                //             $set('nombres', $prospecto->nombres);
                                //             $set('ape_paterno', $prospecto->ape_paterno);
                                //             $set('ape_materno', $prospecto->ape_materno);
                                //             $set('razon_social', $prospecto->razon_social);
                                //             $set('celular', $prospecto->celular);
                                //             $set('correo', $prospecto->correo_electronico);
                                //         } else {
                                //             \Filament\Notifications\Notification::make()
                                //                 ->title('Prospecto no encontrado')
                                //                 ->warning()
                                //                 ->send();
                                //             $set('prospecto_id', null);
                                //         }
                                //     }
                                // }),

                            Hidden::make('prospecto_id'),
                            TextInput::make('nombres')->label('Nombres'),
                            TextInput::make('ape_paterno')->label('Apellido Paterno'),
                            TextInput::make('ape_materno')->label('Apellido Materno'),
                            TextInput::make('razon_social')->label('Razón Social'),
                            Select::make('genero_id')->label('Género')->relationship('genero', 'nombre'),
                            DatePicker::make('fecha_nacimiento')->label('Fecha de Nacimiento'),
                            Select::make('nacionalidad_id')->label('Nacionalidad')->relationship('nacionalidad', 'nombre'),
                            Select::make('estado_civil_id')->label('Estado Civil')->relationship('estadoCivil', 'nombre'),
                            Select::make('grado_estudio_id')->label('Grado de Estudio')->relationship('gradoEstudio', 'nombre'),
                            TextInput::make('telefono')->label('Teléfono'),
                            TextInput::make('celular')->label('Celular'),
                            TextInput::make('direccion')->label('Dirección'),
                            Select::make('departamento_ubigeo_id')
                                ->label('Departamento')
                                ->options(function () {
                                    return \App\Models\DepartamentoUbigeo::orderBy('nombre')->pluck('nombre', 'id');
                                })
                                ->reactive()
                                ->afterStateUpdated(fn (callable $set) => $set('provincia_id', null))
                                ->searchable(),

                            Select::make('provincia_id')
                                ->label('Provincia')
                                ->options(function (callable $get) {
                                    $departamentoId = $get('departamento_ubigeo_id');
                                    if (!$departamentoId) return [];

                                    return \App\Models\Provincia::where('departamento_ubigeo_id', $departamentoId)
                                        ->orderBy('nombre')
                                        ->pluck('nombre', 'id');
                                })
                                ->reactive()
                                ->afterStateUpdated(fn (callable $set) => $set('distrito_id', null))
                                ->searchable(),

                            Select::make('distrito_id')
                                ->label('Distrito')
                                ->options(function (callable $get) {
                                    $provinciaId = $get('provincia_id');
                                    if (!$provinciaId) return [];

                                    return \App\Models\Distrito::where('provincia_id', $provinciaId)
                                        ->orderBy('nombre')
                                        ->pluck('nombre', 'id');
                                })
                                ->searchable(),
                            TextInput::make('direccion_adicional')->label('Dirección Adicional'),
                            Hidden::make('created_by'),
                            Hidden::make('updated_by'),
                        ])
                    ]),

                    Tab::make('Inmueble')->schema([
                        Grid::make(3)->schema([
                            // PROYECTO
                            Select::make('proyecto_id')
                                ->label('Proyecto')
                                ->relationship('proyecto', 'nombre')
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(fn ($set) => $set('departamento_id', null)),

                            // DEPARTAMENTO
                            Select::make('departamento_id')
                                ->label('Inmueble')
                                ->options(function ($get) {
                                    $proyectoId = $get('proyecto_id');
                                    if (!$proyectoId) return [];

                                    return \App\Models\Departamento::with(['edificio', 'tipoInmueble', 'estadoDepartamento'])
                                        ->where('proyecto_id', $proyectoId)
                                        ->whereHas('estadoDepartamento', function ($query) {
                                            $query->where('nombre', 'Disponible');
                                        })
                                        ->get()
                                        ->mapWithKeys(function ($departamento) {
                                            $label = "EDIFICIO: {$departamento->edificio->nombre} - " .
                                                    "TIPO: {$departamento->tipoInmueble->nombre} - " .
                                                    "NRO: {$departamento->num_departamento} - " .
                                                    "CANT. HAB.: {$departamento->num_dormitorios}";
                                            return [$departamento->id => $label];
                                        })
                                        ->toArray();
                                })
                                ->searchable()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function (callable $set, $state) {
                                    if ($state) {
                                        $departamento = \App\Models\Departamento::find($state);
                                        if ($departamento) {
                                            $set('precio_lista', $departamento->Precio_lista);
                                            $set('precio_venta', $departamento->precio);
                                            $set('descuento', $departamento->descuento);
                                        }
                                    }
                                }),

                            // CAMPOS AUTOCOMPLETADOS
                            TextInput::make('precio_lista')
                                ->label('Precio Lista')
                                ->disabled()->dehydrated(false),

                            TextInput::make('precio_venta')
                                ->label('Precio Venta')
                                ->disabled()->dehydrated(false),

                            TextInput::make('descuento')
                                ->label('Descuento')
                                ->numeric()
                                ->suffix('%')
                                ->nullable()
                                ->minValue(0)
                                ->maxValue(5)
                                ->helperText('Ingrese un descuento entre 0% y 5% (opcional)'),

                            // CAMPOS MANUALES
                            TextInput::make('monto_separacion')
                                ->label('Monto de Separación')
                               // ->required()
                                ->numeric()
                                ->minValue(500)
                                ->maxValue(2000)
                                ->helperText('Debe estar entre 500 y 2000'),
                            TextInput::make('monto_cuota_inicial')->label('Monto de Cuota Inicial'),
                            DatePicker::make('fecha_vencimiento')
                                ->label('Fecha de Vencimiento')
                                ->displayFormat('d/m/Y')
                                ->format('Y-m-d')
                                ->nullable()
                                ->default(now()->addDays(2)),


                        ])
                    ]),

                    Tab::make('Observaciones')->schema([
                        Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->rows(6),
                    ]),

                    Tab::make('Documentos')->schema([
                        FileUpload::make('documentos')
                            ->label('Documentos Adjuntos')
                            ->multiple()
                            ->directory('proformas/documentos')
                            ->preserveFilenames()
                            ->enableOpen()
                            ->enableDownload()
                            ->maxSize(10240),
                    ]),
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo_formateado')->label('ID Proforma'),
                Tables\Columns\TextColumn::make('numero_documento')->label('Documento'),
                Tables\Columns\TextColumn::make('nombre_completo')
                ->label('Cliente')
                ->getStateUsing(fn ($record) => $record->nombres . ' ' . $record->ape_paterno),
                Tables\Columns\TextColumn::make('proyecto.nombre')->label('Proyecto'),
                Tables\Columns\TextColumn::make('departamento.num_departamento')->label('Inmueble'),
                Tables\Columns\TextColumn::make('departamento.Precio_venta')->label('Precio Venta')->formatStateUsing(fn ($state) => number_format($state, 2, '.', ',')),
                Tables\Columns\TextColumn::make('monto_separacion')->label('Separación')->formatStateUsing(fn ($state) => number_format($state, 2, '.', ',')),
                Tables\Columns\TextColumn::make('monto_cuota_inicial')->label('Cuota Inicial')->formatStateUsing(fn ($state) => number_format($state, 2, '.', ',')),
                Tables\Columns\TextColumn::make('created_at')->label('Creado')->date(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('generar_pdf')
                    ->label('Generar PDF')
                    ->icon('heroicon-o-document-download')
                    ->color('success')
                    ->action(function (Proforma $record) {
                        // Cargar las relaciones necesarias
                        $proforma = $record->load([
                            'tipoDocumento',
                            'genero',
                            'nacionalidad',
                            'estadoCivil',
                            'gradoEstudio',
                            'ubigeoDepartamento',
                            'ubigeoProvincia',
                            'ubigeoDistrito',
                            'proyecto',
                            'departamento.edificio',
                            'departamento.tipoInmueble'
                        ]);

                        // Generar el PDF
                        $pdf = Pdf::loadView('pdf.proforma', compact('proforma'));

                        // Configurar el PDF
                        $pdf->setPaper('A4', 'portrait');

                        // Descargar el PDF
                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            "proforma_{$proforma->codigo_formateado}.pdf"
                        );
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProformas::route('/'),
            'create' => Pages\CreateProforma::route('/create'),
            'edit' => Pages\EditProforma::route('/{record}/edit'),
        ];
    }
}
