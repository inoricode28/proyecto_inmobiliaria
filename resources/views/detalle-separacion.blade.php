<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Separación - Departamento {{ $departamento->num_departamento }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <!-- Botones superiores -->
        <div class="flex gap-2 mb-6">
            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">PASAR A VENTA</button>
            <button class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Información del Contacto</button>
            <div class="ml-auto flex gap-2">
                <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">📄 Descargar Documento</button>
                <button class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">✏️ Editar OC</button>
                <button class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">🗑️ Ir a</button>
                <button onclick="window.close()" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">✖️ Cerrar</button>
            </div>
        </div>

        <!-- Detalle de Fechas -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-bold mb-4 text-blue-600 border-l-4 border-blue-600 pl-2">Detalle de Fechas</h3>
            
            <!-- Timeline -->
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
                
                <!-- Visita - Sección eliminada o comentada -->
                {{-- 
                <div class="flex">
                    <div class="bg-gray-400 text-white px-4 py-2 text-sm font-bold">Visita</div>
                    <div class="bg-gray-100 px-4 py-2 text-xs border border-gray-300">
                        <div>Sin información de visita disponible</div>
                    </div>
                </div>
                --}}
                
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
        </div>

        <!-- Detalle de Operación Comercial -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold mb-4 text-blue-600 border-l-4 border-blue-600 pl-2">Detalle de Operación Comercial</h3>
            
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

            <!-- Información adicional del departamento -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="font-bold mb-3">Información del Departamento</h4>
                <div class="grid grid-cols-3 gap-4">
                    <div><strong>Proyecto:</strong> {{ $departamento->proyecto->nombre ?? 'N/A' }}</div>
                    <div><strong>Número:</strong> {{ $departamento->num_departamento }}</div>
                    <div><strong>Precio:</strong> S/ {{ number_format($departamento->precio, 2) }}</div>
                    <div><strong>Área:</strong> {{ $departamento->construida }}m²</div>
                    <div><strong>Dormitorios:</strong> {{ $departamento->num_dormitorios }}</div>
                    <div><strong>Baños:</strong> {{ $departamento->num_bano }}</div>
                </div>
            </div>

            <!-- Información del cliente -->
            @if($separacion->proforma)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="font-bold mb-3">Información del Cliente</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div><strong>Documento:</strong> {{ $separacion->proforma->tipoDocumento->nombre ?? 'N/A' }} - {{ $separacion->proforma->numero_documento }}</div>
                    <div><strong>Nombres:</strong> {{ $separacion->proforma->nombres }} {{ $separacion->proforma->ape_paterno }} {{ $separacion->proforma->ape_materno }}</div>
                    <div><strong>Email:</strong> {{ $separacion->proforma->email }}</div>
                    <div><strong>Teléfono:</strong> {{ $separacion->proforma->celular }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>
</body>
</html>