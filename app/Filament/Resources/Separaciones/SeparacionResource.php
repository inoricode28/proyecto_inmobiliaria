<?php

namespace App\Filament\Resources\Separaciones;

use App\Models\Separacion;
use App\Models\Proforma;
use App\Models\Genero;
use App\Models\Ocupacion;
use App\Models\Profesion;
use App\Models\Categoria;
use App\Models\Banco;
use App\Models\DepartamentoUbigeo;
use App\Models\Provincia;
use App\Models\Distrito;

use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Actions\Action;
use Filament\Resources\Form;

use Filament\Resources\Table;

use Filament\Resources\Resource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;

class SeparacionResource extends Resource
{
    protected static ?string $model = Separacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Separaciones';
    protected static ?string $navigationGroup = 'Ventas';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Campo oculto para detectar si viene desde separación definitiva
            Forms\Components\Hidden::make('from_separacion_definitiva')
                ->default(false),

            Tabs::make('Separación')
                ->columnSpan('full')
            ->tabs([
                Tab::make('Cliente')->schema([
                    Grid::make(3)->schema([
                        Select::make('proforma_id')
                            ->label('Proforma')
                            ->options(function () {
                                return Proforma::all()->mapWithKeys(function ($proforma) {
                                    $codigo = 'PRO' . str_pad($proforma->id, 5, '0', STR_PAD_LEFT);
                                    $identificador = $proforma->numero_documento ?: $proforma->razon_social;
                                    return [$proforma->id => "$codigo - $identificador"];
                                });
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $proforma = \App\Models\Proforma::find($state);
                                if ($proforma) {
                                    $set('tipo_documento_nombre', optional($proforma->tipoDocumento)->nombre);
                                    $set('numero_documento', $proforma->numero_documento);
                                    $set('nombres', $proforma->nombres);
                                    $set('ape_paterno', $proforma->ape_paterno);
                                    $set('ape_materno', $proforma->ape_materno);
                                    $set('razon_social', $proforma->razon_social);
                                    $set('genero_id', $proforma->genero_id);
                                    $set('genero_nombre', optional($proforma->genero)->nombre);
                                    $set('fecha_nacimiento', $proforma->fecha_nacimiento);
                                    $set('nacionalidad_nombre', optional($proforma->nacionalidad)->nombre);
                                    $set('estado_civil_id', $proforma->estado_civil_id);
                                    $set('estadoCivil_nombre', optional($proforma->estadoCivil)->nombre);
                                    $set('gradoEstudio_nombre', optional($proforma->gradoEstudio)->nombre);
                                    $set('telefono_casa', $proforma->telefono_casa);
                                    $set('celular', $proforma->celular);
                                    $set('email', $proforma->email);
                                    $set('direccion', $proforma->direccion);
                                    $set('departamento_ubigeo_id', $proforma->departamento_ubigeo_id);
                                    $set('provincia_id', $proforma->provincia_id);
                                    $set('distrito_id', $proforma->distrito_id);
                                    $set('direccion_adicional', $proforma->direccion_adicional);
                                    $set('monto_separacion', $proforma->monto_separacion);
                                    $set('cuota_inicial', $proforma->monto_cuota_inicial);

                                    $set('proyecto_nombre', optional($proforma->proyecto)->nombre);
                                    $set('departamento_nombre', optional($proforma->departamento)->num_departamento);
                                    // precio_lista debe obtener el valor del inmueble (departamento)
                                    $set('precio_lista', $proforma->departamento->Precio_lista);
                                    // precio_venta debe obtener el valor de la proforma asociada (precio con descuento aplicado)
                                    $precioLista = $proforma->departamento->Precio_lista ?? 0;
                                    $descuento = $proforma->descuento ?? 0;
                                    $precioVenta = $precioLista - (($descuento * $precioLista) / 100);
                                    $set('precio_venta', $precioVenta);
                                    $set('descuento', $proforma->descuento); // Obtener descuento de la proforma, no del departamento
                                    $set('departamento_id', $proforma->departamento_id); // Cargar departamento_id
                                }
                            }),

                        TextInput::make('tipo_documento_nombre')->label('Tipo de Documento')->disabled()->dehydrated(false),
                        TextInput::make('numero_documento')->label('N° Documento')->disabled()->dehydrated(false),
                        TextInput::make('nombres')->label('Nombres')->disabled()->dehydrated(false),
                        TextInput::make('ape_paterno')->label('Apellido Paterno')->disabled()->dehydrated(false),
                        TextInput::make('ape_materno')->label('Apellido Materno')->disabled()->dehydrated(false),
                        TextInput::make('razon_social')->label('Razón Social')->disabled()->dehydrated(false),
                        TextInput::make('genero_nombre')->label('Género')->disabled()->dehydrated(false),
                        DatePicker::make('fecha_nacimiento')->label('Fecha de Nacimiento')->disabled(),
                        TextInput::make('nacionalidad_nombre')->label('Nacionalidad')->disabled()->dehydrated(false),
                        TextInput::make('estadoCivil_nombre')->label('Estado Civil')->disabled()->dehydrated(false),
                        TextInput::make('gradoEstudio_nombre')->label('Grado Estudio')->disabled()->dehydrated(false),
                        TextInput::make('telefono_casa')->label('Teléfono')->disabled()->dehydrated(false),
                        TextInput::make('celular')->label('Celular')->disabled()->dehydrated(false),
                        TextInput::make('email')->label('Correo')->disabled()->dehydrated(false),
                        TextInput::make('direccion')->label('Dirección')->disabled()->dehydrated(false),
                        Select::make('departamento_ubigeo_id')->label('Departamento')->relationship('ubigeoDepartamento', 'nombre')->searchable()->preload()->disabled()->dehydrated(false),
                        Select::make('provincia_id')->label('Provincia')->relationship('ubigeoProvincia', 'nombre')->searchable()->preload()->disabled()->dehydrated(false),
                        Select::make('distrito_id')->label('Distrito')->relationship('ubigeoDistrito', 'nombre')->searchable()->preload()->disabled()->dehydrated(false),
                        TextInput::make('direccion_adicional')->label('Dirección Adicional')->disabled()->dehydrated(false),

                        // Campos adicionales de separación
                        Radio::make('tipo_separacion')
                            ->label('Tipo de Separación')
                            ->options([
                                'Separación de Bienes' => 'Separación de Bienes',
                                'Con poderes' => 'Con poderes',
                                'Divorciado' => 'Divorciado',
                                'Ninguno' => 'Ninguno',
                            ])
                            ->inline(),

                        TextInput::make('numero_partida')->label('Número de Partida'),
                        TextInput::make('lugar_partida')->label('Lugar de Partida'),
                        TextInput::make('porcentaje_copropietario')->label('% Co-Propietario'),
                        Select::make('ocupacion_id')->label('Ocupación')->relationship('ocupacion', 'nombre')->searchable()->preload(),
                        Select::make('profesion_id')->label('Profesión')->relationship('profesion', 'nombre')->searchable()->preload(),
                        TextInput::make('puesto')->label('Puesto'),
                        Select::make('categoria_id')->label('Categoría')->relationship('categoria', 'nombre')->searchable()->preload(),
                        TextInput::make('ruc')->label('RUC'),
                        TextInput::make('empresa')->label('Empresa'),
                        TextInput::make('pep')->label('PEP'),
                        DatePicker::make('fecha_pep')->label('Fecha PEP'),
                        TextInput::make('direccion_laboral')->label('Dirección Laboral'),
                        TextInput::make('urbanizacion')->label('Urbanización'),
                        TextInput::make('telefono1')->label('Teléfono 1'),
                        TextInput::make('telefono2')->label('Teléfono 2'),
                        TextInput::make('antiguedad_laboral')->label('Antigüedad Laboral'),
                        TextInput::make('ingresos')->label('Ingresos'),
                        Hidden::make('created_by'),
                        Hidden::make('updated_by'),
                    ]),
                ]),

                Tab::make('Inmueble')->schema([
                    // Tabla de propiedades de la proforma
                    Forms\Components\ViewField::make('propiedades_tabla')
                        ->label('Propiedades de la Proforma')
                        ->view('filament.components.propiedades-tabla-separacion')
                        ->columnSpan('full'),

                    // Campos ocultos para mantener los datos necesarios
                    Hidden::make('departamento_id'),
                    Hidden::make('proyecto_nombre'),
                    Hidden::make('departamento_nombre'),
                    Hidden::make('precio_lista'),
                    Hidden::make('descuento'),
                    Hidden::make('precio_venta'),
                    Hidden::make('monto_separacion'),
                    Hidden::make('cuota_inicial'),
                    Hidden::make('saldo_financiar'),

                    Hidden::make('inmuebles_seleccionados')
                    ->default([]),

                    /*
                    DatePicker::make('fecha_vencimiento')
                        ->label('Fecha de Vencimiento')
                        ->displayFormat('d/m/Y')
                        ->format('Y-m-d')
                        ->nullable()
                        ->default(now()),
                        */
                    // Botones de Cronograma - Visibles cuando hay una proforma seleccionada Y NO viene desde separación definitiva
                    Forms\Components\Placeholder::make('cronograma_actions')
                        ->label('')
                        ->content(function (callable $get) {
                            $proformaId = $get('proforma_id');
                            $display = $proformaId ? 'inline-flex' : 'none';

                            return new \Illuminate\Support\HtmlString('
                                <div class="flex gap-2">
                                    <button type="button"
                                            onclick="console.log(\'🔥 Botón CRONOGRAMA C.I. clickeado\');
                                                     console.log(\'🔍 Verificando función:\', typeof window.openCronogramaModal);
                                                     if(typeof window.openCronogramaModal === \'function\') {
                                                         console.log(\'✅ Función encontrada, ejecutando...\');
                                                         try {
                                                             window.openCronogramaModal();
                                                             console.log(\'✅ openCronogramaModal ejecutada sin errores\');
                                                         } catch(error) {
                                                             console.error(\'❌ Error al ejecutar openCronogramaModal:\', error);
                                                         }
                                                     } else {
                                                         console.error(\'❌ Función openCronogramaModal no encontrada\');
                                                     }"
                                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                            style="display: ' . $display . ';">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Cronograma C.I.
                                    </button>
                                    <button type="button"
                                            onclick="window.dispatchEvent(new CustomEvent(\'open-modal\', { detail: { id: \'cronograma-sf-modal\' } }))"
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                            style="display: ' . $display . ';">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                        Cronograma S.F.
                                    </button>
                                    <button type="button"
                                            onclick="window.dispatchEvent(new CustomEvent(\'open-modal\', { detail: { id: \'pago-separacion-modal\' } }))"
                                            class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                            style="display: ' . $display . ';">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                        Registro de Pago Sep.
                                    </button>
                                </div>
                            ');
                        })
                        ->columnSpanFull()
                        ->reactive()
                        ->visible(function (callable $get) {
                            $fromSeparacionDefinitiva = $get('from_separacion_definitiva') ?? false;
                            // Mostrar botones SOLO cuando viene desde separación definitiva
                            return $get('proforma_id') !== null && $fromSeparacionDefinitiva;
                        }),
                ]),

                Tab::make('Observaciones')->schema([
                    Textarea::make('observaciones')->rows(5)->label('Observaciones'),
                ]),

                Tab::make('Documentos')->schema([
                    FileUpload::make('documentos')
                        ->label('Documentos Adjuntos')
                        ->multiple()
                        ->directory('separaciones/documentos')
                        ->preserveFilenames()
                        ->enableOpen()
                        ->enableDownload()
                        ->maxSize(10240),
                ]),

                Tab::make('Notaría Kardex')->schema([
                    Grid::make(2)->schema([
                        TextInput::make('notaria_kardex.notaria')->label('Notaría'),
                        TextInput::make('notaria_kardex.responsable')->label('Responsable'),
                        TextInput::make('notaria_kardex.direccion')->label('Dirección'),
                        TextInput::make('notaria_kardex.email')->label('Email'),
                        TextInput::make('notaria_kardex.celular')->label('Celular'),
                        TextInput::make('notaria_kardex.telefono')->label('Teléfono'),
                        TextInput::make('notaria_kardex.numero_kardex')->label('Nº Kardex'),
                        TextInput::make('notaria_kardex.oficina')->label('Oficina'),
                        TextInput::make('notaria_kardex.numero_registro')->label('Nº Registro'),
                        TextInput::make('notaria_kardex.agencia')->label('Agencia'),
                        TextInput::make('notaria_kardex.asesor')->label('Asesor'),
                        TextInput::make('notaria_kardex.telefonos')->label('Teléfonos'),
                        TextInput::make('notaria_kardex.correos')->label('Correos'),
                        DatePicker::make('notaria_kardex.fecha_vencimiento_carta')->label('Fec. Venc. Carta Aprobación'),
                        DatePicker::make('notaria_kardex.fecha_escritura')->label('Fec. Escritura Pública'),
                        TextInput::make('notaria_kardex.penalidad_entrega')->label('Penalidad de Entrega'),
                    ]),
                ]),

                Tab::make('Carta Fianza')->schema([
                    Grid::make(2)->schema([
                        Select::make('carta_fianza.banco_id')->label('Banco')->relationship('banco', 'nombre')->searchable()->preload(),
                        TextInput::make('carta_fianza.monto')->label('Monto'),
                        TextInput::make('carta_fianza.numero_carta')->label('Nº Carta Fianza'),
                    ]),
                ]),
            ])
        ]);
    }

    private static function actualizarSaldoFinanciar(callable $set, callable $get): void
    {
        $venta = floatval($get('precio_venta'));
        $sep = floatval($get('monto_separacion'));
        $cuota = floatval($get('cuota_inicial'));
        $saldo = $venta - $sep - $cuota;

        $set('saldo_financiar', number_format($saldo, 2, '.', ''));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('N° Separación')
                    ->formatStateUsing(fn ($state) => 'SEP' . str_pad($state, 5, '0', STR_PAD_LEFT)),
                TextColumn::make('proforma.id')
                    ->label('N° Proforma')
                    ->formatStateUsing(fn ($state) => 'PRO' . str_pad($state, 5, '0', STR_PAD_LEFT)),
                TextColumn::make('proforma.numero_documento')
                    ->label('N° Documento'),
                TextColumn::make('proforma.nombres')
                    ->label('Nombres')
                    ->formatStateUsing(function ($state, $record) {
                        return $record->proforma->nombres . ' ' . $record->proforma->ape_paterno;
                    }),
                TextColumn::make('proforma.proyecto.nombre')
                    ->label('Proyecto'),
                TextColumn::make('proforma.departamento.num_departamento')
                    ->label('Inmueble'),
                TextColumn::make('created_at')
                    ->label('Fecha Registro')
                    ->date('d/m/Y'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeparacions::route('/'),
            'create' => Pages\CreateSeparacion::route('/create'),
            'edit' => Pages\EditSeparacion::route('/{record}/edit'),
        ];
    }
}
