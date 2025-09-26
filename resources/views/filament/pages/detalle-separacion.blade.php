<x-filament::page>
    <style>
    .breadcrumb-container {
        display: flex;
        align-items: center;
        margin-bottom: 24px;
    }

    .breadcrumb-step {
        position: relative;
        padding: 12px 30px;
        background: #6b7280;
        color: white;
        font-weight: bold;
        text-align: center;
        flex: 1;
        clip-path: polygon(0 0, calc(100% - 20px) 0, 100% 50%, calc(100% - 20px) 100%, 0 100%, 20px 50%);
    }

    .breadcrumb-step:first-child {
        clip-path: polygon(0 0, calc(100% - 20px) 0, 100% 50%, calc(100% - 20px) 100%, 0 100%);
    }

    .breadcrumb-step:last-child {
        clip-path: polygon(20px 0, 100% 0, 100% 100%, 20px 100%, 0 50%);
    }

    .breadcrumb-step.active {
        background: #f97316;
    }

    /* Navigation Tabs Styles */
    .nav-tabs {
        display: flex;
        background: #f3f4f6;
        border-radius: 4px;
        padding: 2px;
        margin-bottom: 10px;
        gap: 1px;
        overflow-x: auto;
        scrollbar-width: none;
        -ms-overflow-style: none;
        width: 100%;
    }

    .nav-tabs::-webkit-scrollbar {
        display: none;
    }

    .nav-tab {
        flex: 1;
        min-width: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: 6px 8px;
        background: white;
        border: 1px solid #e5e7eb;
        color: #6b7280;
        font-weight: 500;
        font-size: 10px;
        border-radius: 0;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .nav-tab svg {
        width: 12px;
        height: 12px;
        flex-shrink: 0;
    }

    .nav-tab:hover {
        background: #f3f4f6;
        color: #374151;
    }

    .nav-tab.active {
        background: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }

    .nav-tab:first-child {
        border-top-left-radius: 6px;
        border-bottom-left-radius: 6px;
    }

    .nav-tab:last-child {
        border-top-right-radius: 6px;
        border-bottom-right-radius: 6px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .nav-tab {
            font-size: 11px;
            padding: 6px 8px;
            gap: 4px;
        }

        .nav-tab svg {
            width: 12px;
            height: 12px;
        }
    }

    /* Content Sections */
    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }
    </style>
    <div class="space-y-6">
        <!-- Botones superiores -->
        <div class="flex gap-2 mb-6">
            @if(!$tieneVenta)
                <x-filament::button color="primary" x-data="{}"
                    x-on:click="$dispatch('open-modal', { id: 'confirmar-venta-modal' })">
                    PASAR A VENTA
                </x-filament::button>
            @else
                @if($entregaExistente)
                    {{-- Si ya existe una entrega, ir al formulario de edición --}}
                    <x-filament::button color="warning" tag="a"
                        href="{{ route('filament.resources.entregas.edit', ['record' => $entregaExistente->id]) }}">
                        ENTREGA DE INMUEBLE
                    </x-filament::button>
                @else
                    {{-- Si no existe entrega, crear una nueva --}}
                    <x-filament::button color="success" tag="a"
                        href="{{ route('filament.resources.entregas.create', ['venta_id' => $separacion->venta->id]) }}">
                        ENTREGA DE INMUEBLE
                    </x-filament::button>
                @endif
            @endif
            
            <x-filament::button color="gray" tag="a"
                href="{{ route('filament.resources.panel-seguimiento.view-prospecto-info', ['record' => $separacion->proforma->prospecto->id ?? $separacion->proforma->id]) }}">
                Información del Contacto
            </x-filament::button>
            
            <div class="ml-auto flex gap-2">
                <x-filament::button color="primary" icon="heroicon-o-eye">
                    Descargar Documento
                </x-filament::button>
                <x-filament::button color="gray" icon="heroicon-o-pencil">
                    Editar OC
                </x-filament::button>
                <x-filament::button color="danger" icon="heroicon-o-trash">
                    Eliminar
                </x-filament::button>
            </div>
        </div>



        <!-- Detalle de Fechas - Siempre visible -->
        <x-filament::card>
            <x-slot name="heading">
                Detalle de Fechas
            </x-slot>

            <!-- Progress Steps -->
            <div class="breadcrumb-container">
                <div class="breadcrumb-step">
                    Proforma
                </div>
                <div class="breadcrumb-step">
                    Visita
                </div>
                <div class="breadcrumb-step active">
                    Separación Definitiva
                </div>
                @if($tieneVenta)
                <div class="breadcrumb-step" style="background: #4ac204;">
                    Venta
                </div>
                @endif
                @if($entregaExistente)
                <div class="breadcrumb-step" style="background: #28a745;">
                    Entrega
                </div>
                @endif
            </div>

            <!-- Date Details -->
            <div class="grid {{ $entregaExistente ? 'grid-cols-5' : ($tieneVenta ? 'grid-cols-4' : 'grid-cols-3') }} gap-4">
                <!-- Proforma Details -->
                <div class="bg-gray-100 p-4 rounded border">
                    <h4 class="font-bold text-gray-700 mb-2">Proforma</h4>
                    <div class="text-xs space-y-1">
                        <div><strong>Fecha Creación:</strong><br>{{ $separacion->proforma->created_at->format('d/m/Y H:i') }}</div>
                        <div><strong>Fecha Proforma:</strong><br>{{ $separacion->proforma->created_at->format('d/m/Y H:i') }}</div>
                        <div><strong>Fecha de Vencimiento:</strong><br>{{ $separacion->proforma->fecha_vencimiento ? $separacion->proforma->fecha_vencimiento->format('d/m/Y H:i') : 'N/A' }}</div>
                    </div>
                </div>

                <!-- Visita Details -->
                <div class="bg-gray-100 p-4 rounded border">
                    <h4 class="font-bold text-gray-700 mb-2">Visita</h4>
                    <div class="text-xs space-y-1">
                        <div><strong>Fecha Visita:</strong><br>{{ $separacion->proforma->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>

                <!-- Separación Definitiva Details -->
                <div class="bg-orange-100 p-4 rounded border">
                    <h4 class="font-bold text-orange-700 mb-2">Separación Definitiva</h4>
                    <div class="text-xs space-y-1">
                        <div><strong>Fecha Creación:</strong><br>{{ $separacion->created_at->format('d/m/Y H:i') }}</div>
                        <div><strong>Fecha Separación Definitiva:</strong><br>{{ $separacion->created_at->format('d/m/Y H:i') }}</div>
                        <div><strong>Fecha de Vencimiento:</strong><br>{{ $separacion->fecha_vencimiento ? $separacion->fecha_vencimiento->format('d/m/Y H:i') : 'N/A' }}</div>
                    </div>
                </div>
                
                @if($tieneVenta)
                <!-- Venta Details -->
                <div class="bg-green-100 p-4 rounded border">
                    <h4 class="font-bold text-green-700 mb-2">Venta</h4>
                    <div class="text-xs space-y-1">
                        <div><strong>Fecha Creación:</strong><br>{{ $separacion->venta->created_at->format('d/m/Y H:i') }}</div>
                        <div><strong>Fecha Venta:</strong><br>{{ $separacion->venta->fecha_venta ? $separacion->venta->fecha_venta->format('d/m/Y H:i') : 'N/A' }}</div>
                        <div><strong>Fecha Preminuta:</strong><br>{{ $separacion->venta->fecha_preminuta ? $separacion->venta->fecha_preminuta->format('d/m/Y H:i') : 'N/A' }}</div>
                        <div><strong>Fecha Minuta:</strong><br>{{ $separacion->venta->fecha_minuta ? $separacion->venta->fecha_minuta->format('d/m/Y H:i') : 'N/A' }}</div>
                    </div>
                </div>
                @endif

                @if($entregaExistente)
                <!-- Entrega Details -->
                <div class="bg-blue-100 p-4 rounded border">
                    <h4 class="font-bold text-blue-700 mb-2">Entrega</h4>
                    <div class="text-xs space-y-1">
                        <div><strong>Fecha Entrega:</strong><br>{{ $entregaExistente->fecha_entrega ? $entregaExistente->fecha_entrega->format('d/m/Y') : 'N/A' }}</div>
                      <!--  <div><strong>Fecha Entrega Minuta:</strong><br>{{ $separacion->venta->fecha_minuta ? $separacion->venta->fecha_minuta->format('d/m/Y') : 'N/A' }}</div>
                        <div><strong>Garantía Acabados:</strong><br>{{ $entregaExistente->fecha_garantia_acabados ? $entregaExistente->fecha_garantia_acabados->format('d/m/Y') : 'N/A' }}</div>
                        <div><strong>Garantía Vicios Ocultos:</strong><br>{{ $entregaExistente->fecha_garantia_vicios_ocultos ? $entregaExistente->fecha_garantia_vicios_ocultos->format('d/m/Y') : 'N/A' }}</div>

                      -->
                    </div>
                </div>
                @endif
            </div>
        </x-filament::card>

        <!-- Detalle de Operación Comercial - Siempre visible -->
        <x-filament::card>
            <x-slot name="heading">
                Detalle de Operación Comercial
            </x-slot>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <div class="mb-2"><strong>Tipo Cotización:</strong> PRESENCIAL</div>
                    <div class="mb-2"><strong>Etapa Comercial:</strong> SEPARACION
                        DEFINITIVA*{{ str_pad($separacion->id, 6, '0', STR_PAD_LEFT) }}</div>
                    <div class="mb-2"><strong>Vendedor:</strong>
                        {{ $separacion->proforma->prospecto->nombres ?? 'N/A' }}
                        {{ $separacion->proforma->prospecto->ape_paterno ?? '' }}</div>
                </div>
                <div>
                    <div class="mb-2"><strong>Fecha Separación:</strong> {{ $separacion->created_at->format('d/m/Y') }}
                    </div>
                    <div class="mb-2"><strong>Monto Separación:</strong> S/
                        {{ number_format($separacion->monto_separacion ?? 0, 2) }}</div>
                    <div class="mb-2"><strong>Estado:</strong> {{ $separacion->estado ?? 'ACTIVO' }}</div>
                </div>
            </div>
        </x-filament::card>

        <!-- Navigation Tabs -->
        <div class="nav-tabs">
            <div class="nav-tab active" data-tab="cliente">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Cliente
            </div>
            <div class="nav-tab" data-tab="inmueble">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                    </path>
                </svg>
                Inmueble
            </div>
            <div class="nav-tab" data-tab="estado-cuenta">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                    </path>
                </svg>
                Est. Cuenta
            </div>
            <div class="nav-tab" data-tab="devolucion">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                </svg>
                Devolución
            </div>
            <div class="nav-tab" data-tab="notaria">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l7-3 7 3z"></path>
                </svg>
                Notaría y Kardex/Banco
            </div>
            <div class="nav-tab" data-tab="carta-fianza">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                Carta Fianza
            </div>
            <div class="nav-tab" data-tab="marketing">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
                Marketing
            </div>
            <div class="nav-tab" data-tab="hitos">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                    </path>
                </svg>
                Hitos
            </div>
            <div class="nav-tab" data-tab="observacion">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                    </path>
                </svg>
                Observación
            </div>
            <div class="nav-tab" data-tab="documentos">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                Documentos
            </div>
            <div class="nav-tab" data-tab="historial">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Historial
            </div>
            <div class="nav-tab" data-tab="cronograma-sf">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                Cronograma SF
            </div>
        </div>

        <!-- Tab Content Sections -->

        <!-- Cliente Tab Content -->
        <div class="tab-content active" id="cliente-content">

            <!-- Información del cliente -->
            @if($separacion->proforma)
            <x-filament::card>
                <x-slot name="heading">
                    Titular: {{ $separacion->proforma->nombres }} {{ $separacion->proforma->ape_paterno }}
                    {{ $separacion->proforma->ape_materno }}
                </x-slot>

                <div class="text-sm text-gray-600 mb-4">
                    {{ $separacion->proforma->tipoDocumento->nombre ?? 'DNI' }}
                    {{ $separacion->proforma->numero_documento }}
                    ({{ $separacion->proforma->nacionalidad->nombre ?? 'PERU' }})
                </div>

                <div class="grid grid-cols-3 gap-6">
                    <!-- Contacto -->
                    <div>
                        <h4 class="text-blue-600 font-semibold mb-3 border-l-4 border-blue-500 pl-3">Contacto</h4>
                        <div class="space-y-2 text-sm">
                            <div><strong>Teléfono Casa:</strong></div>
                            <div><strong>Celular:</strong> {{ $separacion->proforma->celular ?? '' }} /</div>
                            <div><strong>Email:</strong> {{ $separacion->proforma->email ?? '' }}</div>
                            <div><strong>Fecha Nac.:</strong>
                                {{ $separacion->proforma->fecha_nacimiento ? \Carbon\Carbon::parse($separacion->proforma->fecha_nacimiento)->format('d/m/Y') : '' }}
                            </div>
                            <div><strong>Dirección:</strong> {{ $separacion->proforma->direccion ?? '' }}</div>
                            <div><strong>Dirección Adicional:</strong></div>
                        </div>
                    </div>

                    <!-- Otros datos -->
                    <div>
                        <h4 class="text-blue-600 font-semibold mb-3 border-l-4 border-blue-500 pl-3">Otros datos</h4>
                        <div class="space-y-2 text-sm">
                            <div><strong>Estado Civil:</strong>
                                {{ $separacion->proforma->estadoCivil->nombre ?? 'Casado(a)' }}</div>
                            <div><strong>Género:</strong> {{ $separacion->proforma->genero->nombre ?? 'Masculino' }}
                            </div>
                            <div><strong>Separación de Bienes:</strong> No</div>
                            <div><strong>Con Poderes:</strong> No</div>
                            <div><strong>Divorciado:</strong> No</div>
                            <div><strong>Ninguno:</strong> No</div>
                            <div><strong>Otro Doc Ident:</strong></div>
                        </div>
                    </div>

                    <!-- Información Laboral -->
                    <div>
                        <h4 class="text-blue-600 font-semibold mb-3 border-l-4 border-blue-500 pl-3">Información Laboral
                        </h4>
                        <div class="space-y-2 text-sm">
                            <div><strong>Ocupación:</strong></div>
                            <div><strong>Profesión:</strong> {{ $separacion->proforma->profesion ?? 'Docente' }}</div>
                            <div><strong>Puesto:</strong> {{ $separacion->proforma->puesto ?? 'Cirujano dentista' }}
                            </div>
                            <div><strong>Categoría:</strong> 0 °</div>
                            <div><strong>RUC:</strong></div>
                            <div><strong>Empresa:</strong></div>
                            <div><strong>Ingresos:</strong> 0.00</div>
                            <div><strong>Antigüedad Laboral:</strong> 0</div>
                            <div><strong>Teléfonos:</strong> /</div>
                            <div><strong>Dirección Laboral:</strong></div>
                            <div class="mt-4">/ / /</div>
                        </div>
                    </div>
                </div>
            </x-filament::card>
            @endif
        </div>

        <!-- Inmueble Tab Content -->
        <div class="tab-content" id="inmueble-content">
            <!-- Información del Departamento -->
            <x-filament::card>
                <x-slot name="heading">
                    Información del Departamento
                </x-slot>

                <div class="grid grid-cols-3 gap-4">
                    <div><strong>Proyecto:</strong> {{ $departamento->proyecto->nombre ?? 'N/A' }}</div>
                    <div><strong>Número:</strong> {{ $departamento->num_departamento }}</div>
                    <div><strong>Precio:</strong> S/ {{ number_format($departamento->precio, 2) }}</div>
                    <div><strong>Área:</strong> {{ $departamento->construida }}m²</div>
                    <div><strong>Dormitorios:</strong> {{ $departamento->num_dormitorios }}</div>
                    <div><strong>Baños:</strong> {{ $departamento->num_bano }}</div>
                </div>
            </x-filament::card>

            <!-- Detalles Adicionales del Inmueble -->
            <x-filament::card>
                <x-slot name="heading">
                    Detalles Financieros
                </x-slot>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <div class="mb-2"><strong>Tipo Financiamiento:</strong>
                            {{ $departamento->tipoFinanciamiento->nombre ?? 'Contado' }}</div>
                        <div class="mb-2"><strong>Entidad Financiera:</strong> Con La Inmobiliaria</div>
                        <div class="mb-2"><strong>Estado Inmueble:</strong>
                            {{ $departamento->estadoDepartamento->nombre ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="mb-2"><strong>Precio Total:</strong> S/
                            {{ number_format($departamento->precio, 2) }}</div>
                        <div class="mb-2"><strong>Monto Separación:</strong> S/
                            {{ number_format($separacion->monto_separacion ?? 0, 2) }}</div>
                        <div class="mb-2"><strong>Saldo Pendiente:</strong> S/
                            {{ number_format(($departamento->precio - ($separacion->monto_separacion ?? 0)), 2) }}</div>
                    </div>
                </div>
            </x-filament::card>
        </div>

        <!-- Estado Cuenta Tab Content -->
        <div class="tab-content" id="estado-cuenta-content">
            <x-filament::card>
                <x-slot name="heading">
                    Estado de Cuenta
                </x-slot>
                <div class="p-6 text-center text-gray-500">
                    <p>Información del estado de cuenta del cliente.</p>
                    <p class="text-sm mt-2">Esta sección mostrará los pagos, saldos pendientes y historial financiero.
                    </p>
                </div>
            </x-filament::card>
        </div>

        <!-- Devolución Tab Content -->
        <div class="tab-content" id="devolucion-content">
            <x-filament::card>
                <x-slot name="heading">
                    Devoluciones
                </x-slot>
                <div class="p-6 text-center text-gray-500">
                    <p>Información sobre devoluciones y reembolsos.</p>
                    <p class="text-sm mt-2">Esta sección mostrará el historial de devoluciones y procesos de reembolso.
                    </p>
                </div>
            </x-filament::card>
        </div>

        <!-- Notaría Tab Content -->
        <div class="tab-content" id="notaria-content">
            <x-filament::card>
                <x-slot name="heading">
                    Notaría y Kardex/Banco
                </x-slot>
                <div class="p-6 text-center text-gray-500">
                    <p>Información notarial y bancaria.</p>
                    <p class="text-sm mt-2">Esta sección mostrará documentos notariales y información bancaria.</p>
                </div>
            </x-filament::card>
        </div>

        <!-- Carta Fianza Tab Content -->
        <div class="tab-content" id="carta-fianza-content">
            <x-filament::card>
                <x-slot name="heading">
                    Carta Fianza
                </x-slot>
                <div class="p-6 text-center text-gray-500">
                    <p>Información sobre cartas fianza.</p>
                    <p class="text-sm mt-2">Esta sección mostrará las cartas fianza asociadas al cliente.</p>
                </div>
            </x-filament::card>
        </div>

        <!-- Marketing Tab Content -->
        <div class="tab-content" id="marketing-content">
            <x-filament::card>
                <x-slot name="heading">
                    Marketing
                </x-slot>
                <div class="p-6 text-center text-gray-500">
                    <p>Información de marketing y campañas.</p>
                    <p class="text-sm mt-2">Esta sección mostrará las campañas de marketing asociadas.</p>
                </div>
            </x-filament::card>
        </div>

        <!-- Hitos Tab Content -->
        <div class="tab-content" id="hitos-content">
            <x-filament::card>
                <x-slot name="heading">
                    Hitos
                </x-slot>
                <div class="p-6 text-center text-gray-500">
                    <p>Hitos importantes del proceso.</p>
                    <p class="text-sm mt-2">Esta sección mostrará los hitos y fechas importantes del proceso de venta.
                    </p>
                </div>
            </x-filament::card>
        </div>

        <!-- Observación Tab Content -->
        <div class="tab-content" id="observacion-content">
            <x-filament::card>
                <x-slot name="heading">
                    Observaciones
                </x-slot>
                <div class="p-6 text-center text-gray-500">
                    <p>Observaciones y notas importantes.</p>
                    <p class="text-sm mt-2">Esta sección mostrará las observaciones registradas durante el proceso.</p>
                </div>
            </x-filament::card>
        </div>

        <!-- Documentos Tab Content -->
        <div class="tab-content" id="documentos-content">
            <x-filament::card>
                <x-slot name="heading">
                    Documentos
                </x-slot>
                <div class="p-6 text-center text-gray-500">
                    <p>Documentos asociados al proceso.</p>
                    <p class="text-sm mt-2">Esta sección mostrará todos los documentos relacionados con la separación.
                    </p>
                </div>
            </x-filament::card>
        </div>

        <!-- Historial Tab Content -->
        <div class="tab-content" id="historial-content">
            <x-filament::card>
                <x-slot name="heading">
                    Historial
                </x-slot>
                <div class="p-6 text-center text-gray-500">
                    <p>Historial completo de actividades.</p>
                    <p class="text-sm mt-2">Esta sección mostrará el historial cronológico de todas las actividades.</p>
                </div>
            </x-filament::card>
        </div>

        <!-- Cronograma SF Tab Content -->
        <div class="tab-content" id="cronograma-sf-content">
            <x-filament::card>
                <x-slot name="heading">
                    Cronograma de Saldo a Financiar
                </x-slot>
                
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-4">
                        <div class="text-sm text-gray-600">
                            <strong>Separación ID:</strong> {{ $separacion->id }}
                        </div>
                        <button type="button" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm"
                                onclick="loadCronogramaSF()">
                            Actualizar Cronograma
                        </button>
                    </div>
                    
                    <!-- Cuotas Temporales -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3 border-l-4 border-yellow-500 pl-3">
                            Cuotas Temporales
                        </h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200 rounded-lg" id="cuotas-temporales-sf-table">
                                <thead class="bg-yellow-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Cuota</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Fecha Vencimiento</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Monto</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="cuotas-temporales-sf-body">
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                            No hay cuotas temporales registradas
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Cuotas Definitivas -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3 border-l-4 border-green-500 pl-3">
                            Cuotas Definitivas
                        </h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200 rounded-lg" id="cuotas-definitivas-sf-table">
                                <thead class="bg-green-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Cuota</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Fecha Vencimiento</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Monto</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="cuotas-definitivas-sf-body">
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                            No hay cuotas definitivas registradas
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </x-filament::card>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Obtener todas las pestañas y contenidos
        const tabs = document.querySelectorAll('.nav-tab');
        const contents = document.querySelectorAll('.tab-content');

        // Agregar event listener a cada pestaña
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Remover clase active de todas las pestañas
                tabs.forEach(t => t.classList.remove('active'));

                // Agregar clase active a la pestaña clickeada
                this.classList.add('active');

                // Obtener el ID del contenido correspondiente
                const targetTab = this.getAttribute('data-tab');
                const targetContent = document.getElementById(targetTab + '-content');

                // Ocultar todos los contenidos
                contents.forEach(content => {
                    content.classList.remove('active');
                });

                // Mostrar el contenido correspondiente
                if (targetContent) {
                    targetContent.classList.add('active');
                }

                // Si es el tab de cronograma SF, cargar los datos
                if (targetTab === 'cronograma-sf') {
                    loadCronogramaSF();
                }
            });
        });
    });

    // Función para cargar el cronograma de saldo a financiar
    function loadCronogramaSF() {
        const separacionId = {{ $separacion->id }};
        
        // Cargar cuotas temporales
        fetch(`/cronograma/sf/temporales/${separacionId}`)
            .then(response => response.json())
            .then(data => {
                displayCuotasTemporalesSF(data);
            })
            .catch(error => {
                console.error('Error al cargar cuotas temporales SF:', error);
                displayErrorMessage('cuotas-temporales-sf-body', 'Error al cargar cuotas temporales');
            });

        // Cargar cuotas definitivas
        fetch(`/cronograma/sf/definitivas/${separacionId}`)
            .then(response => response.json())
            .then(data => {
                displayCuotasDefinitivasSF(data);
            })
            .catch(error => {
                console.error('Error al cargar cuotas definitivas SF:', error);
                displayErrorMessage('cuotas-definitivas-sf-body', 'Error al cargar cuotas definitivas');
            });
    }

    // Función para mostrar cuotas temporales SF
    function displayCuotasTemporalesSF(cuotas) {
        const tbody = document.getElementById('cuotas-temporales-sf-body');
        
        if (!cuotas || cuotas.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                        No hay cuotas temporales registradas
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = cuotas.map(cuota => `
            <tr class="hover:bg-yellow-50">
                <td class="px-4 py-2 border-b text-sm">${cuota.numero_cuota}</td>
                <td class="px-4 py-2 border-b text-sm">${formatDate(cuota.fecha_vencimiento)}</td>
                <td class="px-4 py-2 border-b text-sm font-semibold">S/ ${formatNumber(cuota.monto)}</td>
                <td class="px-4 py-2 border-b text-sm">
                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                        ${cuota.estado || 'Temporal'}
                    </span>
                </td>
            </tr>
        `).join('');
    }

    // Función para mostrar cuotas definitivas SF
    function displayCuotasDefinitivasSF(cuotas) {
        const tbody = document.getElementById('cuotas-definitivas-sf-body');
        
        if (!cuotas || cuotas.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                        No hay cuotas definitivas registradas
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = cuotas.map(cuota => `
            <tr class="hover:bg-green-50">
                <td class="px-4 py-2 border-b text-sm">${cuota.numero_cuota}</td>
                <td class="px-4 py-2 border-b text-sm">${formatDate(cuota.fecha_vencimiento)}</td>
                <td class="px-4 py-2 border-b text-sm font-semibold">S/ ${formatNumber(cuota.monto)}</td>
                <td class="px-4 py-2 border-b text-sm">
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                        ${cuota.estado || 'Definitiva'}
                    </span>
                </td>
            </tr>
        `).join('');
    }

    // Función para mostrar mensaje de error
    function displayErrorMessage(tbodyId, message) {
        const tbody = document.getElementById(tbodyId);
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="px-4 py-8 text-center text-red-500">
                    ${message}
                </td>
            </tr>
        `;
    }

    // Función para formatear fechas
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('es-PE', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }

    // Función para formatear números
    function formatNumber(number) {
        if (!number) return '0.00';
        return parseFloat(number).toLocaleString('es-PE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
    </script>

    <!-- Modal de Confirmación para Pasar a Venta -->
    <x-filament::modal id="confirmar-venta-modal" width="md">
        <x-slot name="heading">
            Confirmación
        </x-slot>

        <div class="py-4">
            <p class="text-gray-700">¿Está seguro de pasar a Ventas?</p>
        </div>

        <x-slot name="footer">
            <div class="flex gap-3 justify-end">
                <x-filament::button color="danger"
                    x-on:click="$dispatch('close-modal', { id: 'confirmar-venta-modal' })">
                    CANCELAR
                </x-filament::button>
                <x-filament::button color="primary" x-on:click="confirmarPasarAVenta()">
                    ACEPTAR
                </x-filament::button>
            </div>
        </x-slot>
    </x-filament::modal>

    <script>
    function confirmarPasarAVenta() {
        // Cerrar el modal
        window.dispatchEvent(new CustomEvent('close-modal', {
            detail: {
                id: 'confirmar-venta-modal'
            }
        }));

        // Redirigir al formulario de creación de venta con la separación preseleccionada
        const separacionId = '{{ $separacion->id }}';
        const createVentaUrl = '/ventas/create?separacion_id=' + separacionId;
        window.location.href = createVentaUrl;
    }
    </script>

</x-filament::page>
