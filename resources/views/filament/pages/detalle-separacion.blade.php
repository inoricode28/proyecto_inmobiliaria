<x-filament::page>
    <div class="space-y-6">
        <!-- Botones superiores -->
        <div class="flex gap-2 mb-6">
            <x-filament::button color="primary">
                PASAR A VENTA
            </x-filament::button>
            <x-filament::button color="gray">
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

        <!-- Detalle de Fechas -->
        <x-filament::card>
            <x-slot name="heading">
                Detalle de Fechas
            </x-slot>
            
            <div class="flex items-center space-x-0 mb-4">
                <!-- Proforma -->
                <div class="flex">
                    <div class="bg-gray-400 text-white px-4 py-2 text-sm font-bold">Proforma</div>
                    <div class="bg-gray-100 px-4 py-2 text-xs border border-gray-300">
                        <div><strong>Fecha Creación:</strong> {{ $separacion->proforma->created_at->format('d/m/Y H:i') }}</div>
                        <div><strong>Fecha Proforma:</strong> {{ $separacion->proforma->created_at->format('d/m/Y H:i') }}</div>
                        <div><strong>Fecha de Vencimiento:</strong> {{ $separacion->proforma->fecha_vencimiento ? $separacion->proforma->fecha_vencimiento->format('d/m/Y H:i') : 'N/A' }}</div>
                    </div>
                </div>
                
                <!-- Separación Definitiva -->
                <div class="flex">
                    <div class="bg-yellow-500 text-white px-4 py-2 text-sm font-bold">Separación Definitiva</div>
                    <div class="bg-yellow-100 px-4 py-2 text-xs border border-yellow-300">
                        <div><strong>Fecha Creación:</strong> {{ $separacion->created_at->format('d/m/Y H:i') }}</div>
                        <div><strong>Fecha Separación:</strong> {{ $separacion->created_at->format('d/m/Y H:i') }}</div>
                        <div><strong>Fecha de Vencimiento:</strong> {{ $separacion->fecha_vencimiento ? $separacion->fecha_vencimiento->format('d/m/Y H:i') : 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </x-filament::card>

        <!-- Detalle de Operación Comercial -->
        <x-filament::card>
            <x-slot name="heading">
                Detalle de Operación Comercial
            </x-slot>
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <div class="mb-2"><strong>Tipo Cotización:</strong> PRESENCIAL</div>
                    <div class="mb-2"><strong>Etapa Comercial:</strong> SEPARACION DEFINITIVA*{{ str_pad($separacion->id, 6, '0', STR_PAD_LEFT) }}</div>
                    <div class="mb-2"><strong>Vendedor:</strong> {{ $separacion->proforma->prospecto->nombres ?? 'N/A' }} {{ $separacion->proforma->prospecto->ape_paterno ?? '' }}</div>
                </div>
                <div>
                    <div class="mb-2"><strong>Tipo Financiamiento:</strong> {{ $departamento->tipoFinanciamiento->nombre ?? 'Contado' }}</div>
                    <div class="mb-2"><strong>Entidad Financiera:</strong> Con La Inmobiliaria</div>
                    <div class="mb-2"><strong>Estado Inmueble:</strong> {{ $departamento->estadoDepartamento->nombre ?? 'N/A' }}</div>
                </div>
            </div>
        </x-filament::card>

        <!-- Información adicional del departamento -->
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

        <!-- Información del cliente -->
        @if($separacion->proforma)
        <x-filament::card>
            <x-slot name="heading">
                Información del Cliente
            </x-slot>
            
            <div class="grid grid-cols-2 gap-4">
                <div><strong>Documento:</strong> {{ $separacion->proforma->tipoDocumento->nombre ?? 'N/A' }} - {{ $separacion->proforma->numero_documento }}</div>
                <div><strong>Nombres:</strong> {{ $separacion->proforma->nombres }} {{ $separacion->proforma->ape_paterno }} {{ $separacion->proforma->ape_materno }}</div>
                <div><strong>Email:</strong> {{ $separacion->proforma->email }}</div>
                <div><strong>Teléfono:</strong> {{ $separacion->proforma->celular }}</div>
            </div>
        </x-filament::card>
        @endif
    </div>
</x-filament::page>