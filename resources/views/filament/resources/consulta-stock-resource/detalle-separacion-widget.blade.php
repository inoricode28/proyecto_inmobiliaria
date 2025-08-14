<div class="fixed inset-0 z-50 flex items-center justify-center" 
     x-data="{ modalAbierto: @entangle('modalAbierto') }" 
     x-show="modalAbierto" 
     x-cloak
     @modal-abierto.window="modalAbierto = true"
     style="background-color: rgba(0, 0, 0, 0.5);">
     
    <div class="w-[85vw] h-[90vh] overflow-y-auto bg-white rounded-lg shadow border p-6" 
         @click.stop>
         
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Detalle de Separación</h2>
                <button wire:click="cerrarModal" 
                        class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            @if($proformaData && $separacionData)
                <!-- Detalle de Fechas -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-blue-600 mb-4 border-l-4 border-blue-500 pl-3">Detalle de Fechas</h3>
                    
                    <!-- Progress Steps -->
                    <div class="flex items-center justify-center mb-6">
                        <div class="flex items-center">
                            <!-- Proforma -->
                            <div class="flex flex-col items-center">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                    1
                                </div>
                                <span class="text-xs mt-1">Proforma</span>
                            </div>
                            
                            <!-- Línea -->
                            <div class="w-16 h-1 bg-green-500 mx-2"></div>
                            
                            <!-- Separación -->
                            <div class="flex flex-col items-center">
                                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                    2
                                </div>
                                <span class="text-xs mt-1">Separación</span>
                            </div>
                            
                            <!-- Línea -->
                            <div class="w-16 h-1 bg-gray-300 mx-2"></div>
                            
                            <!-- Minuta -->
                            <div class="flex flex-col items-center">
                                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                    3
                                </div>
                                <span class="text-xs mt-1">Minuta</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-4">
                        <!-- Visita -->
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h4 class="font-semibold mb-2">Visita</h4>
                            <div class="space-y-1 text-sm">
                                <div><strong>Fecha Visita:</strong> {{ $visitaData['fecha_visita'] ?? 'N/A' }}</div>
                            </div>
                        </div>
                        
                        <!-- Proforma -->
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h4 class="font-semibold mb-2">Proforma</h4>
                            <div class="space-y-1 text-sm">
                                <div><strong>Fecha Proforma:</strong> {{ $visitaData['fecha_proforma'] ?? 'N/A' }}</div>
                            </div>
                        </div>
                        
                        <!-- Separación Definitiva -->
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <h4 class="font-semibold mb-2">Separación Definitiva</h4>
                            <div class="space-y-1 text-sm">
                                <div><strong>Fecha Creación:</strong> {{ $separacionData->created_at->format('d/m/Y H:i') }}</div>
                                <div><strong>Fecha Separación Definitiva:</strong> {{ $separacionData->created_at->format('d/m/Y H:i') }}</div>
                                <div><strong>Fecha de Vencimiento:</strong> {{ $visitaData['fecha_vencimiento'] ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detalle de Operación Comercial -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-blue-600 mb-4 border-l-4 border-blue-500 pl-3">Detalle de Operación Comercial</h3>
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <div><strong>Tipo Cotización:</strong> PRESENCIAL</div>
                            <div><strong>Etapa Comercial:</strong> SEPARACION DEFINITIVA*{{ str_pad($separacionData->id ?? 0, 6, '0', STR_PAD_LEFT) }}</div>
                            <div><strong>Vendedor:</strong> {{ $proformaData->nombres ?? 'N/A' }} {{ $proformaData->ape_paterno ?? '' }}</div>
                        </div>
                        
                        <div class="space-y-3">
                            <div><strong>Tipo Separación:</strong> {{ $separacionData->tipo_separacion ?? 'N/A' }}</div>
                            <div><strong>RUC:</strong> {{ $separacionData->ruc ?? 'N/A' }}</div>
                            <div><strong>Empresa:</strong> {{ $separacionData->empresa ?? 'N/A' }}</div>
                            <div><strong>Ingresos:</strong> S/ {{ number_format($separacionData->ingresos ?? 0, 2) }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Información del Inmueble -->
                <div>
                    <h3 class="text-lg font-semibold text-blue-600 mb-4 border-l-4 border-blue-500 pl-3">Información del Inmueble</h3>
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <div><strong>Proyecto:</strong> {{ $proformaData->proyecto->nombre ?? $proformaData->departamento->proyecto->nombre ?? 'N/A' }}</div>
                            <div><strong>Departamento:</strong> {{ $proformaData->departamento->num_departamento ?? 'N/A' }}</div>
                            <div><strong>Precio Lista:</strong> S/ {{ number_format($proformaData->departamento->precio ?? 0, 2) }}</div>
                        </div>
                        
                        <div class="space-y-3">
                            <div><strong>Monto Separación:</strong> S/ {{ number_format($proformaData->monto_separacion ?? 0, 2) }}</div>
                            <div><strong>Saldo a Financiar:</strong> S/ {{ number_format($separacionData->saldo_a_financiar ?? 0, 2) }}</div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-500">No se encontraron datos de separación para este inmueble.</p>
                    <p class="text-sm text-gray-400 mt-2">Departamento ID: {{ $departamentoId }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>