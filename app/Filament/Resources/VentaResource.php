<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VentaResource\Pages;
use App\Models\Venta;
use App\Models\Separacion;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View;
use Illuminate\Support\HtmlString;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;

class VentaResource extends Resource
{
    protected static ?string $model = Venta::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $modelLabel = 'Venta';
    protected static ?string $pluralModelLabel = 'Ventas';
    protected static ?string $navigationGroup = 'Ventas';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Vista Blade personalizada para el header de separación
            View::make('filament.components.venta-header')
                ->columnSpan('full'),

                Forms\Components\Hidden::make('fecha_entrega_inicial'),
                Forms\Components\Hidden::make('fecha_venta'),
                Forms\Components\Hidden::make('fecha_preminuta'),
                Forms\Components\Hidden::make('fecha_minuta'),
            // Campo oculto para separacion_id
                        Hidden::make('separacion_id')
                            ->required()
                            ->reactive()
                                ->afterStateUpdated(function ($state, callable $set, $livewire) {
                                    if ($state) {
                                        $separacion = Separacion::with([
                                            'proforma.tipoDocumento',
                                            'proforma.genero',
                                            'proforma.nacionalidad',
                                            'proforma.estadoCivil',
                                            'proforma.gradoEstudio',
                                            'proforma.proyecto',
                                            'proforma.departamento',
                                            'ocupacion',
                                            'profesion',
                                            'categoria',
                                            'notariaKardex',
                                            'cartaFianza'
                                        ])->find($state);

                                        // Actualizar la vista Blade con los datos de la separación


                                        if ($separacion && $separacion->proforma) {
                                            $proforma = $separacion->proforma;

                                            // Datos del cliente
                                            $set('tipo_documento_nombre', optional($proforma->tipoDocumento)->nombre);
                                            $set('numero_documento', $proforma->numero_documento);
                                            $set('nombres', $proforma->nombres);
                                            $set('ape_paterno', $proforma->ape_paterno);
                                            $set('ape_materno', $proforma->ape_materno);
                                            $set('razon_social', $proforma->razon_social);
                                            $set('genero_nombre', optional($proforma->genero)->nombre);
                                            $set('fecha_nacimiento', $proforma->fecha_nacimiento);
                                            $set('nacionalidad_nombre', optional($proforma->nacionalidad)->nombre);
                                            $set('estadoCivil_nombre', optional($proforma->estadoCivil)->nombre);
                                            $set('gradoEstudio_nombre', optional($proforma->gradoEstudio)->nombre);
                                            $set('telefono_casa', $proforma->telefono_casa);
                                            $set('celular', $proforma->celular);
                                            $set('email', $proforma->email);
                                            $set('direccion', $proforma->direccion);
                                            $set('direccion_adicional', $proforma->direccion_adicional);

                                            // Datos del inmueble
                                            $set('proyecto_nombre', optional($proforma->proyecto)->nombre);
                                            $set('departamento_nombre', optional($proforma->departamento)->num_departamento);

                                            // Datos de separación
                                            $set('observaciones_separacion', $separacion->observaciones);
                                            $set('documentos_separacion', $separacion->documentos);

                                            // Datos de notaría kardex
                                            if ($separacion->notariaKardex) {
                                                $notaria = $separacion->notariaKardex;
                                                $set('notaria_nombre', $notaria->notaria);
                                                $set('notaria_responsable', $notaria->responsable);
                                                $set('notaria_direccion', $notaria->direccion);
                                                $set('notaria_email', $notaria->email);
                                                $set('notaria_celular', $notaria->celular);
                                                $set('notaria_telefono', $notaria->telefono);
                                                $set('numero_kardex', $notaria->numero_kardex);
                                                $set('oficina', $notaria->oficina);
                                                $set('numero_registro', $notaria->numero_registro);
                                                $set('agencia', $notaria->agencia);
                                                $set('asesor', $notaria->asesor);
                                                $set('telefonos', $notaria->telefonos);
                                                $set('correos', $notaria->correos);
                                                $set('fecha_vencimiento_carta', $notaria->fecha_vencimiento_carta);
                                                $set('fecha_escritura', $notaria->fecha_escritura_publica);
                                                $set('penalidad_entrega', $notaria->penalidad_entrega);
                                            }

                                            // Datos de carta fianza
                                            if ($separacion->cartaFianza) {
                                                $carta = $separacion->cartaFianza;
                                                $set('carta_banco_nombre', optional($carta->banco)->nombre);
                                                $set('carta_monto', $carta->monto);
                                                $set('carta_numero', $carta->numero_carta);
                                            }
                                            $set('precio_lista', $proforma->departamento->precio ?? 0);
                                            $set('precio_venta', $proforma->departamento->Precio_venta ?? 0);
                                            $set('descuento', $proforma->departamento->descuento ?? 0);
                                            $set('monto_separacion', $proforma->monto_separacion);
                                            $set('cuota_inicial', $proforma->monto_cuota_inicial);

                                            // Datos de separación
                                            $set('tipo_separacion', $separacion->tipo_separacion);
                                            $set('numero_partida', $separacion->numero_partida);
                                            $set('lugar_partida', $separacion->lugar_partida);
                                            $set('porcentaje_copropietario', $separacion->porcentaje_copropietario);
                                            $set('ocupacion_nombre', optional($separacion->ocupacion)->nombre);
                                            $set('profesion_nombre', optional($separacion->profesion)->nombre);
                                            $set('puesto', $separacion->puesto);
                                            $set('categoria_nombre', optional($separacion->categoria)->nombre);
                                            $set('ruc', $separacion->ruc);
                                            $set('empresa', $separacion->empresa);
                                            $set('pep', $separacion->pep);
                                            $set('fecha_pep', $separacion->fecha_pep);
                                            $set('direccion_laboral', $separacion->direccion_laboral);
                                            $set('urbanizacion', $separacion->urbanizacion);
                                            $set('telefono1', $separacion->telefono1);
                                            $set('telefono2', $separacion->telefono2);
                                            $set('antiguedad_laboral', $separacion->antiguedad_laboral);
                                            $set('ingresos', $separacion->ingresos);
                                            $set('saldo_a_financiar', $separacion->saldo_a_financiar);

                            // Datos de ubicación
                            $set('departamento_ubigeo_nombre', optional($proforma->ubigeoDepartamento)->nombre);
                            $set('provincia_nombre', optional($proforma->ubigeoProvincia)->nombre);
                            $set('distrito_nombre', optional($proforma->ubigeoDistrito)->nombre);
                                        }
                                    }
                                }),

            // Campos de estado y fechas (ocultos, manejados por el header)
            Select::make('estado')
                ->label('Estado')
                ->options([
                    'activo' => 'Activo',
                    'inactivo' => 'Inactivo'
                ])
                ->default('activo')
                ->required()
                ->hidden(),

            DatePicker::make('fecha_entrega_inicial')
                ->label('Fecha Entrega Inicial')
                ->displayFormat('d/m/Y')
                ->nullable()
                ->hidden()
                ->dehydrated(),

            DatePicker::make('fecha_venta')
                ->label('Fecha Venta')
                ->displayFormat('d/m/Y')
                ->nullable()
                ->hidden()
                ->dehydrated(),

            DatePicker::make('fecha_preminuta')
                ->label('Fecha Pre-minuta')
                ->displayFormat('d/m/Y')
                ->nullable()
                ->hidden()
                ->dehydrated(),

            DatePicker::make('fecha_minuta')
                ->label('Fecha Minuta')
                ->displayFormat('d/m/Y')
                ->nullable()
                ->hidden()
                ->dehydrated(),

            Tabs::make('Venta')
                ->columnSpan('full')
                ->tabs([

                    Tab::make('Cliente')->schema([
                        Grid::make(3)->schema([
                            TextInput::make('tipo_documento_nombre')->label('Tipo de Documento')->disabled()->dehydrated(false),
                            TextInput::make('numero_documento')->label('N° Documento')->disabled()->dehydrated(false),
                            TextInput::make('nombres')->label('Nombres')->disabled()->dehydrated(false),
                            TextInput::make('ape_paterno')->label('Apellido Paterno')->disabled()->dehydrated(false),
                            TextInput::make('ape_materno')->label('Apellido Materno')->disabled()->dehydrated(false),
                            TextInput::make('razon_social')->label('Razón Social')->disabled()->dehydrated(false),
                            TextInput::make('genero_nombre')->label('Género')->disabled()->dehydrated(false),
                            DatePicker::make('fecha_nacimiento')->label('Fecha de Nacimiento')->disabled()->dehydrated(false),
                            TextInput::make('nacionalidad_nombre')->label('Nacionalidad')->disabled()->dehydrated(false),
                            TextInput::make('estadoCivil_nombre')->label('Estado Civil')->disabled()->dehydrated(false),
                            TextInput::make('gradoEstudio_nombre')->label('Grado Estudio')->disabled()->dehydrated(false),
                            TextInput::make('telefono_casa')->label('Teléfono')->disabled()->dehydrated(false),
                            TextInput::make('celular')->label('Celular')->disabled()->dehydrated(false),
                            TextInput::make('email')->label('Correo')->disabled()->dehydrated(false),
                            TextInput::make('direccion')->label('Dirección')->disabled()->dehydrated(false),
                            TextInput::make('departamento_ubigeo_nombre')->label('Departamento')->disabled()->dehydrated(false),
                            TextInput::make('provincia_nombre')->label('Provincia')->disabled()->dehydrated(false),
                            TextInput::make('distrito_nombre')->label('Distrito')->disabled()->dehydrated(false),
                            TextInput::make('direccion_adicional')->label('Dirección Adicional')->disabled()->dehydrated(false),

                            // Campos adicionales de separación
                            TextInput::make('tipo_separacion')->label('Tipo de Separación')->disabled()->dehydrated(false),
                            TextInput::make('numero_partida')->label('Número de Partida')->disabled()->dehydrated(false),
                            TextInput::make('lugar_partida')->label('Lugar de Partida')->disabled()->dehydrated(false),
                            TextInput::make('porcentaje_copropietario')->label('% Co-Propietario')->disabled()->dehydrated(false),
                            TextInput::make('ocupacion_nombre')->label('Ocupación')->disabled()->dehydrated(false),
                            TextInput::make('profesion_nombre')->label('Profesión')->disabled()->dehydrated(false),
                            TextInput::make('puesto')->label('Puesto')->disabled()->dehydrated(false),
                            TextInput::make('categoria_nombre')->label('Categoría')->disabled()->dehydrated(false),
                            TextInput::make('ruc')->label('RUC')->disabled()->dehydrated(false),
                            TextInput::make('empresa')->label('Empresa')->disabled()->dehydrated(false),
                            TextInput::make('pep')->label('PEP')->disabled()->dehydrated(false),
                            DatePicker::make('fecha_pep')->label('Fecha PEP')->disabled()->dehydrated(false),
                            TextInput::make('direccion_laboral')->label('Dirección Laboral')->disabled()->dehydrated(false),
                            TextInput::make('urbanizacion')->label('Urbanización')->disabled()->dehydrated(false),
                            TextInput::make('telefono1')->label('Teléfono 1')->disabled()->dehydrated(false),
                            TextInput::make('telefono2')->label('Teléfono 2')->disabled()->dehydrated(false),
                            TextInput::make('antiguedad_laboral')->label('Antigüedad Laboral')->disabled()->dehydrated(false),
                            TextInput::make('ingresos')->label('Ingresos')->disabled()->dehydrated(false),
                        ]),
                    ]),

                    Tab::make('Inmueble')->schema([
                        Grid::make(3)->schema([
                            TextInput::make('proyecto_nombre')->label('Proyecto')->disabled()->dehydrated(false),
                            TextInput::make('departamento_nombre')->label('Inmueble')->disabled()->dehydrated(false),
                            TextInput::make('precio_lista')->label('Precio Lista')->disabled()->dehydrated(false),
                            TextInput::make('descuento')->label('Descuento')->disabled()->dehydrated(false),
                            TextInput::make('precio_venta')->label('Precio Venta')->disabled()->dehydrated(false),
                            TextInput::make('monto_separacion')->label('Monto de Separación')->disabled()->dehydrated(false),
                            TextInput::make('cuota_inicial')->label('Monto de Cuota Inicial')->disabled()->dehydrated(false),
                            TextInput::make('saldo_a_financiar')->label('Saldo a Financiar')->disabled()->dehydrated(false),
                        ]),
                    ]),

                    Tab::make('Observaciones')->schema([
                        Textarea::make('observaciones_separacion')
                            ->label('Observaciones de la Separación')
                            ->rows(5)
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan('full'),
                    ]),

                    Tab::make('Documentos')->schema([
                        Textarea::make('documentos_separacion')
                            ->label('Documentos de la Separación')
                            ->rows(5)
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan('full'),
                    ]),

                    Tab::make('Notaría Kardex')->schema([
                        Grid::make(2)->schema([
                            TextInput::make('notaria_nombre')
                                ->label('Notaría')
                                ->disabled()
                                ->dehydrated(false),
                            TextInput::make('notaria_responsable')
                                ->label('Responsable')
                                ->disabled()
                                ->dehydrated(false),
                            TextInput::make('notaria_direccion')
                                ->label('Dirección')
                                ->disabled()
                                ->dehydrated(false),
                            TextInput::make('notaria_email')
                                ->label('Email')
                                ->disabled()
                                ->dehydrated(false),
                            TextInput::make('notaria_celular')
                                ->label('Celular')
                                ->disabled()
                                ->dehydrated(false),
                            TextInput::make('notaria_telefono')
                                ->label('Teléfono')
                                ->disabled()
                                ->dehydrated(false),
                            TextInput::make('numero_kardex')
                                ->label('Número Kardex')
                                ->disabled()
                                ->dehydrated(false),
                            TextInput::make('oficina')
                                ->label('Oficina')
                                ->disabled()
                                ->dehydrated(false),
                            TextInput::make('numero_registro')
                                ->label('Número Registro')
                                ->disabled()
                                ->dehydrated(false),
                            TextInput::make('agencia')
                                ->label('Agencia')
                                ->disabled()
                                ->dehydrated(false),
                            TextInput::make('asesor')
                                ->label('Asesor')
                                ->disabled()
                                ->dehydrated(false),
                            TextInput::make('telefonos')
                                ->label('Teléfonos')
                                ->disabled()
                                ->dehydrated(false),
                            TextInput::make('correos')
                                ->label('Correos')
                                ->disabled()
                                ->dehydrated(false),
                            DatePicker::make('fecha_vencimiento_carta')
                                ->label('Fecha Vencimiento Carta')
                                ->disabled()
                                ->dehydrated(false),
                            DatePicker::make('fecha_escritura')
                                ->label('Fecha Escritura Pública')
                                ->disabled()
                                ->dehydrated(false),
                            TextInput::make('penalidad_entrega')
                                ->label('Penalidad Entrega')
                                ->disabled()
                                ->dehydrated(false),
                        ]),
                    ]),

                    Tab::make('Carta Fianza')->schema([
                        Grid::make(2)->schema([
                            TextInput::make('carta_banco_nombre')
                                ->label('Banco')
                                ->disabled()
                                ->dehydrated(false),
                            TextInput::make('carta_monto')
                                ->label('Monto')
                                ->numeric()
                                ->disabled()
                                ->dehydrated(false),
                            TextInput::make('carta_numero')
                                ->label('Número de Carta')
                                ->disabled()
                                ->dehydrated(false),
                        ]),
                    ]),
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('N° Venta')
                    ->formatStateUsing(fn ($state) => 'VEN' . str_pad($state, 5, '0', STR_PAD_LEFT)),

                TextColumn::make('separacion.id')
                    ->label('N° Separación')
                    ->formatStateUsing(fn ($state) => 'SEP' . str_pad($state, 5, '0', STR_PAD_LEFT)),

                TextColumn::make('separacion.proforma.nombres')
                    ->label('Cliente')
                    ->formatStateUsing(function ($state, $record) {
                        $proforma = $record->separacion->proforma ?? null;
                        return $proforma ? $proforma->nombres . ' ' . $proforma->ape_paterno : 'N/A';
                    }),

                TextColumn::make('separacion.proforma.proyecto.nombre')
                    ->label('Proyecto'),

                TextColumn::make('separacion.proforma.departamento.num_departamento')
                    ->label('Inmueble'),

                TextColumn::make('fecha_venta')
                    ->label('Fecha Venta')
                    ->date('d/m/Y'),

                BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'success' => 'activo',
                        'danger' => 'inactivo',
                    ]),

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
            'index' => Pages\ListVentas::route('/'),
            'create' => Pages\CreateVenta::route('/create'),
            'edit' => Pages\EditVenta::route('/{record}/edit'),
        ];
    }
}