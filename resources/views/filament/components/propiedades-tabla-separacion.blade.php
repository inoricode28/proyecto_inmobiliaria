@php
    // Obtener los datos de la proforma seleccionada
    $proformaId = $this->data['proforma_id'] ?? null;
    $proforma = null;
    $inmueblesDisponibles = collect();
    
    if ($proformaId) {
        $proforma = \App\Models\Proforma::with(['departamento.proyecto', 'departamento.tipoInmueble', 'inmuebles.departamento.proyecto', 'inmuebles.departamento.tipoInmueble'])->find($proformaId);
        
        // Obtener todos los inmuebles disponibles de la proforma
        if ($proforma) {
            // Si tiene inmuebles múltiples, usar esos
            if ($proforma->inmuebles && $proforma->inmuebles->count() > 0) {
                $inmueblesDisponibles = $proforma->inmuebles;
            } 
            // Si no, usar el inmueble principal
            elseif ($proforma->departamento) {
                $inmueblesDisponibles = collect([
                    (object)[
                        'id' => $proforma->departamento->id,
                        'departamento' => $proforma->departamento,
                        'descuento_personalizado' => $proforma->descuento,
                        'monto_separacion_personalizado' => $proforma->monto_separacion,
                        'cuota_inicial_personalizada' => $proforma->monto_cuota_inicial
                    ]
                ]);
            }
        }
    }
@endphp

@if($proforma && $inmueblesDisponibles->count() > 0)
    
<div class="mt-4">
    <!-- Selector de Inmuebles -->
    <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
        <div class="flex items-center gap-4">
            <div class="flex-1">
                <label for="property-selector" class="block text-sm font-medium text-gray-700 mb-2">
                    Seleccionar Inmueble para Agregar:
                </label>
                <select id="property-selector" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white">
                    <option value="">-- Seleccione un inmueble --</option>
                    @foreach($inmueblesDisponibles as $inmueble)
                        @php
                            $departamento = $inmueble->departamento;
                            $inmuebleId = $inmueble->id ?? $departamento->id;
                        @endphp
                        <option value="{{ $inmuebleId }}" 
                                data-proyecto="{{ $departamento->proyecto->nombre ?? 'N/A' }}"
                                data-numero="{{ $departamento->num_departamento ?? 'N/A' }}"
                                data-tipo="{{ $departamento->tipoInmueble->nombre ?? '' }}"
                                data-dormitorios="{{ $departamento->num_dormitorios ?? 0 }}"
                                data-area="{{ $departamento->construida ?? 0 }}"
                                data-precio="{{ $departamento->Precio_lista ?? 0 }}"
                                data-descuento="{{ $inmueble->descuento ?? 0 }}"
                                data-separacion="{{ $inmueble->monto_separacion ?? 0 }}"
                                data-cuota-inicial="{{ $inmueble->monto_cuota_inicial ?? 0 }}">
                            {{ $departamento->proyecto->nombre ?? 'N/A' }} - 
                            {{ $departamento->num_departamento ?? 'N/A' }} 
                            ({{ $departamento->tipoInmueble->nombre ?? '' }} - {{ $departamento->num_dormitorios }} dorm.)
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
       
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full border-collapse border border-gray-300 table-fixed">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 px-4 py-2 text-center" style="width: 80px;">Acciones</th>
                    <th class="border border-gray-300 px-4 py-2 text-left" style="width: 120px;">Proyecto</th>
                    <th class="border border-gray-300 px-4 py-2 text-left" style="width: 160px;">Inmueble</th>
                    <th class="border border-gray-300 px-4 py-2 text-right bg-gray-50" style="width: 120px;">Precio Lista</th>
                    <th class="border border-gray-300 px-4 py-2 text-right" style="width: 120px;">Descuento (S/)</th>
                    <th class="border border-gray-300 px-4 py-2 text-right bg-gray-50" style="width: 120px;">Precio Venta</th>
                    <th class="border border-gray-300 px-4 py-2 text-right" style="width: 120px;">Separación</th>
                    <th class="border border-gray-300 px-4 py-2 text-right" style="width: 120px;">Cuota Inicial</th>
                    <th class="border border-gray-300 px-4 py-2 text-right bg-gray-50" style="width: 140px;">Saldo a Financiar</th>
                </tr>
            </thead>
            <tbody id="properties-tbody">
                <!-- Fila por defecto cuando no hay propiedades -->
                <tr id="default-row" class="text-gray-500 italic">
                    <td class="border border-gray-300 px-4 py-2 text-center" style="width: 80px;">-</td>
                    <td class="border border-gray-300 px-4 py-2" style="width: 120px;"></td>
                    <td class="border border-gray-300 px-4 py-2" style="width: 160px;">
                        <div class="font-medium"></div>
                        <div class="text-sm text-gray-400"></div>
                    </td>
                    <td class="border border-gray-300 px-4 py-2 text-right bg-gray-50" style="width: 120px;">S/ 0.00</td>
                    <td class="border border-gray-300 px-4 py-2 text-right" style="width: 120px;">S/ 0.00</td>
                    <td class="border border-gray-300 px-4 py-2 text-right bg-gray-50" style="width: 120px;">S/ 0.00</td>
                    <td class="border border-gray-300 px-4 py-2 text-right" style="width: 120px;">S/ 0.00</td>
                    <td class="border border-gray-300 px-4 py-2 text-right" style="width: 120px;">S/ 0.00</td>
                    <td class="border border-gray-300 px-4 py-2 text-right bg-gray-50" style="width: 140px;">S/ 0.00</td>
                </tr>
            </tbody>
            <tfoot>
                <!-- Fila de totales -->
                <tr class="bg-blue-50 font-bold" id="totals-row" style="display: none;">
                    <td class="border border-gray-300 px-4 py-2" colspan="3">TOTALES</td>
                    <td class="border border-gray-300 px-4 py-2 text-right" id="total-precio-lista">S/ 0.00</td>
                    <td class="border border-gray-300 px-4 py-2 text-right" id="total-descuento">S/ 0.00</td>
                    <td class="border border-gray-300 px-4 py-2 text-right" id="total-precio-venta">S/ 0.00</td>
                    <td class="border border-gray-300 px-4 py-2 text-right" id="total-separacion">S/ 0.00</td>
                    <td class="border border-gray-300 px-4 py-2 text-right" id="total-cuota-inicial">S/ 0.00</td>
                    <td class="border border-gray-300 px-4 py-2 text-right" id="total-saldo-financiar">S/ 0.00</td>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <div id="empty-message" class="p-8 text-center text-gray-500 border border-gray-200 rounded-lg mt-4">
        <p class="text-lg">No hay inmuebles agregados</p>
        <p class="text-sm">Seleccione un inmueble del selector de arriba para agregarlo a la separación.</p>
    </div>
    
    <!-- Botones de Cronograma - Solo visibles cuando hay propiedades agregadas 
    <div id="cronograma-buttons" class="mt-4 flex gap-2 justify-center" style="display: none;">
        <button type="button" 
                onclick="openCronogramaModal()"
                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            CRONOGRAMA C.I.
        </button>
        <button type="button" 
                onclick="openCronogramaSFModal()"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
            CRONOGRAMA S.F.
        </button>
        <button type="button" 
                onclick="openPagoSeparacionModal()"
                class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
            REGISTRO PAGO SEP.
        </button>
    </div>
    -->
    <!-- Nota sobre múltiples inmuebles 
    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded">
        <p class="text-sm text-yellow-800">
            <strong>Nota:</strong> Puede agregar múltiples inmuebles de la proforma y modificar los valores de descuento, separación y cuota inicial. 
            Los cálculos se actualizarán automáticamente.
        </p>
    </div>
    -->
</div>

<script>
let addedProperties = [];
let propertyCounter = 0;

// Agregar automáticamente al seleccionar
document.addEventListener('DOMContentLoaded', function() {
    const selector = document.getElementById('property-selector');
    
    if (selector) {
        selector.addEventListener('change', function() {
            if (this.value) {
                addSelectedProperty();
            }
        });
    }
});

function addSelectedProperty() {
    const selector = document.getElementById('property-selector');
    const selectedOption = selector.options[selector.selectedIndex];
    
    if (!selectedOption.value) return;
    
    const propertyId = selectedOption.value;
    
    // Verificar si ya está agregado
    if (addedProperties.includes(propertyId)) {
        alert('Este inmueble ya ha sido agregado.');
        return;
    }
    
    // Obtener datos del inmueble
    const propertyData = {
        id: propertyId,
        proyecto: selectedOption.dataset.proyecto,
        numero: selectedOption.dataset.numero,
        tipo: selectedOption.dataset.tipo,
        dormitorios: selectedOption.dataset.dormitorios,
        area: selectedOption.dataset.area,
        precio: parseFloat(selectedOption.dataset.precio),
        descuento: parseFloat(selectedOption.dataset.descuento),
        separacion: parseFloat(selectedOption.dataset.separacion),
        cuotaInicial: parseFloat(selectedOption.dataset.cuotaInicial)
    };
    
    // Agregar a la tabla
    addPropertyToTable(propertyData);
    
    // Agregar a la lista de propiedades agregadas
    addedProperties.push(propertyId);
    
    // Deshabilitar la opción en el selector
    selectedOption.disabled = true;
    
    // Resetear selector
    selector.value = '';
    
    // Ocultar mensaje vacío y mostrar totales
    document.getElementById('empty-message').style.display = 'none';
    document.getElementById('totals-row').style.display = '';
    
    // Ocultar fila por defecto
    const defaultRow = document.getElementById('default-row');
    if (defaultRow) {
        defaultRow.style.display = 'none';
    }
    
    // Actualizar cálculos
    updateCalculations();
}

function addPropertyToTable(propertyData) {
    const tbody = document.getElementById('properties-tbody');
    const index = propertyCounter++;
    
    console.log('Agregando propiedad:', propertyData.id, 'Index:', index);
    console.log('Número de filas antes:', tbody.children.length);
    console.log('Contenido actual del tbody:', tbody.innerHTML);
    
    // Calcular el monto del descuento basado en el porcentaje original
    const montoDescuento = propertyData.precio * (propertyData.descuento / 100);
    const precioVenta = propertyData.precio - montoDescuento;
    const saldoFinanciar = precioVenta - propertyData.separacion - propertyData.cuotaInicial;
    
    const row = document.createElement('tr');
    row.className = 'hover:bg-gray-50 property-row';
    row.dataset.index = index;
    row.dataset.propertyId = propertyData.id;
    
    row.innerHTML = `
        <td class="border border-gray-300 px-4 py-2 text-center" style="width: 80px;">
            <button type="button" 
                    class="px-2 py-1 bg-red-500 text-white rounded text-sm hover:bg-red-600"
                    onclick="removeProperty('${propertyData.id}', ${index})">
                Quitar
            </button>
        </td>
        <td class="border border-gray-300 px-4 py-2" style="width: 120px;">
            ${propertyData.proyecto}
        </td>
        <td class="border border-gray-300 px-4 py-2" style="width: 160px;">
            <div class="font-medium">${propertyData.numero}</div>
            <div class="text-sm text-gray-500">
                ${propertyData.tipo} - ${propertyData.dormitorios} dorm. - ${propertyData.area}m²
            </div>
        </td>
        <td class="border border-gray-300 px-4 py-2 text-right precio-lista bg-gray-50" data-value="${propertyData.precio}" style="width: 120px;">
            S/ ${propertyData.precio.toLocaleString('es-PE', {minimumFractionDigits: 2})}
        </td>
        <td class="border border-gray-300 px-4 py-2 text-right" style="width: 120px;">
            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded">
                S/ ${montoDescuento.toFixed(2)}
            </span>
        </td>
        <td class="border border-gray-300 px-4 py-2 text-right precio-venta bg-gray-50" data-value="${precioVenta}" style="width: 120px;">
            S/ ${precioVenta.toLocaleString('es-PE', {minimumFractionDigits: 2})}
        </td>
        <td class="border border-gray-300 px-4 py-2 text-right" style="width: 120px;">
            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded">
                S/ ${propertyData.separacion.toLocaleString('es-PE', {minimumFractionDigits: 2})}
            </span>
        </td>
        <td class="border border-gray-300 px-4 py-2 text-right" style="width: 120px;">
            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded">
                S/ ${propertyData.cuotaInicial.toLocaleString('es-PE', {minimumFractionDigits: 2})}
            </span>
        </td>
        <td class="border border-gray-300 px-4 py-2 text-right saldo-financiar bg-gray-50" data-value="${saldoFinanciar}" style="width: 140px;">
            S/ ${saldoFinanciar.toLocaleString('es-PE', {minimumFractionDigits: 2})}
        </td>
    `;
    
    console.log('HTML de la nueva fila:', row.innerHTML);
    tbody.appendChild(row);
    console.log('Número de filas después:', tbody.children.length);
    console.log('Contenido del tbody después:', tbody.innerHTML);
    console.log('Fila agregada:', row);
}

function removeProperty(propertyId, index) {
    // Remover de la lista de propiedades agregadas
    const propertyIndex = addedProperties.indexOf(propertyId);
    if (propertyIndex > -1) {
        addedProperties.splice(propertyIndex, 1);
    }
    
    // Habilitar la opción en el selector
    const selector = document.getElementById('property-selector');
    const option = selector.querySelector(`option[value="${propertyId}"]`);
    if (option) {
        option.disabled = false;
    }
    
    // Remover la fila de la tabla
    const row = document.querySelector(`[data-index="${index}"]`);
    if (row) {
        row.remove();
    }
    
    // Verificar si quedan propiedades
    if (addedProperties.length === 0) {
        document.getElementById('empty-message').style.display = 'block';
        document.getElementById('totals-row').style.display = 'none';
        
        // Mostrar fila por defecto
        const defaultRow = document.getElementById('default-row');
        if (defaultRow) {
            defaultRow.style.display = '';
        }
    }
    
    // Actualizar cálculos
    updateCalculations();
}

function updateRowCalculations(index) {
    const row = document.querySelector(`[data-index="${index}"]`);
    if (!row) return;
    
    const precioLista = parseFloat(row.querySelector('.precio-lista').dataset.value);
    const montoDescuento = parseFloat(row.querySelector('.descuento-input').value) || 0;
    const separacion = parseFloat(row.querySelector('.separacion-input').value) || 0;
    const cuotaInicial = parseFloat(row.querySelector('.cuota-inicial-input').value) || 0;
    
    const precioVenta = precioLista - montoDescuento;
    const saldoFinanciar = precioVenta - separacion - cuotaInicial;
    
    // Actualizar valores en la fila
    row.querySelector('.precio-venta').textContent = 'S/ ' + precioVenta.toLocaleString('es-PE', {minimumFractionDigits: 2});
    row.querySelector('.precio-venta').dataset.value = precioVenta;
    row.querySelector('.saldo-financiar').textContent = 'S/ ' + saldoFinanciar.toLocaleString('es-PE', {minimumFractionDigits: 2});
    row.querySelector('.saldo-financiar').dataset.value = saldoFinanciar;
    
    updateCalculations();
}

function updateCalculations() {
    let totalPrecioLista = 0;
    let totalDescuento = 0;
    let totalPrecioVenta = 0;
    let totalSeparacion = 0;
    let totalCuotaInicial = 0;
    let totalSaldoFinanciar = 0;
    
    // Obtener todas las filas del tbody (excluyendo la fila por defecto)
    const tbody = document.getElementById('properties-tbody');
    if (!tbody) return;
    
    const rows = tbody.querySelectorAll('tr:not(#default-row)');
    
    rows.forEach(row => {
        // Obtener valores de los elementos con data-value
        const precioListaEl = row.querySelector('.precio-lista');
        const precioVentaEl = row.querySelector('.precio-venta');
        const saldoFinanciarEl = row.querySelector('.saldo-financiar');
        
        const precioLista = precioListaEl ? parseFloat(precioListaEl.dataset.value) || 0 : 0;
        const precioVenta = precioVentaEl ? parseFloat(precioVentaEl.dataset.value) || 0 : 0;
        const saldoFinanciar = saldoFinanciarEl ? parseFloat(saldoFinanciarEl.dataset.value) || 0 : 0;
        
        // Calcular descuento como diferencia entre precio lista y precio venta
        const montoDescuento = precioLista - precioVenta;
        
        // Obtener separación y cuota inicial de los spans (extraer números del texto)
        const separacionSpan = row.querySelector('td:nth-child(7) span');
        const cuotaInicialSpan = row.querySelector('td:nth-child(8) span');
        
        const separacion = separacionSpan ? parseFloat(separacionSpan.textContent.replace(/[^\d.-]/g, '')) || 0 : 0;
        const cuotaInicial = cuotaInicialSpan ? parseFloat(cuotaInicialSpan.textContent.replace(/[^\d.-]/g, '')) || 0 : 0;
        
        totalPrecioLista += precioLista;
        totalDescuento += montoDescuento;
        totalPrecioVenta += precioVenta;
        totalSeparacion += separacion;
        totalCuotaInicial += cuotaInicial;
        totalSaldoFinanciar += saldoFinanciar;
    });
    
    // Actualizar totales en la fila de totales
    const totalElements = {
        'total-precio-lista': totalPrecioLista,
        'total-descuento': totalDescuento,
        'total-precio-venta': totalPrecioVenta,
        'total-separacion': totalSeparacion,
        'total-cuota-inicial': totalCuotaInicial,
        'total-saldo-financiar': totalSaldoFinanciar
    };
    
    Object.entries(totalElements).forEach(([id, value]) => {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = 'S/ ' + value.toLocaleString('es-PE', {minimumFractionDigits: 2});
        }
    });
    
    // Mostrar la fila de totales si hay propiedades
    const totalsRow = document.getElementById('totals-row');
    if (totalsRow) {
        totalsRow.style.display = rows.length > 0 ? 'table-row' : 'none';
    }
    
    // Mostrar/ocultar botones de cronograma
    const cronogramaButtons = document.getElementById('cronograma-buttons');
    if (cronogramaButtons) {
        cronogramaButtons.style.display = rows.length > 0 ? 'flex' : 'none';
    }
}

// Funciones para abrir los modales de cronograma
function openCronogramaModal() {
    // Establecer los datos de múltiples propiedades para el cronograma
    window.multiplePropertiesData = getMultiplePropertiesData();
    window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'cronograma-modal' } }));
}

function openCronogramaSFModal() {
    // Establecer los datos de múltiples propiedades para el cronograma SF
    window.multiplePropertiesData = getMultiplePropertiesData();
    window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'cronograma-sf-modal' } }));
}

function openPagoSeparacionModal() {
    // Establecer los datos de múltiples propiedades para el registro de pagos
    window.multiplePropertiesData = getMultiplePropertiesData();
    window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'pago-separacion-modal' } }));
}

// Función para obtener los datos de múltiples propiedades
function getMultiplePropertiesData() {
    const rows = document.querySelectorAll('#properties-table tbody tr[data-index]');
    const properties = [];
    let totalPrecioLista = 0;
    let totalDescuento = 0;
    let totalPrecioVenta = 0;
    let totalSeparacion = 0;
    let totalCuotaInicial = 0;
    let totalSaldoFinanciar = 0;
    
    rows.forEach(row => {
        const precioListaEl = row.querySelector('.precio-lista');
        const precioVentaEl = row.querySelector('.precio-venta');
        const separacionSpan = row.querySelector('td:nth-child(7) span');
        const cuotaInicialSpan = row.querySelector('td:nth-child(8) span');
        const saldoFinanciarSpan = row.querySelector('td:nth-child(9) span');
        
        const precioLista = precioListaEl ? parseFloat(precioListaEl.dataset.value) || 0 : 0;
        const precioVenta = precioVentaEl ? parseFloat(precioVentaEl.dataset.value) || 0 : 0;
        const montoDescuento = precioLista - precioVenta;
        const separacion = separacionSpan ? parseFloat(separacionSpan.textContent.replace(/[^\d.-]/g, '')) || 0 : 0;
        const cuotaInicial = cuotaInicialSpan ? parseFloat(cuotaInicialSpan.textContent.replace(/[^\d.-]/g, '')) || 0 : 0;
        const saldoFinanciar = saldoFinanciarSpan ? parseFloat(saldoFinanciarSpan.textContent.replace(/[^\d.-]/g, '')) || 0 : 0;
        
        // Obtener información del inmueble
        const inmuebleInfo = row.querySelector('td:nth-child(3)');
        const inmuebleNumero = inmuebleInfo ? inmuebleInfo.querySelector('.font-medium')?.textContent || 'N/A' : 'N/A';
        const inmuebleDetalle = inmuebleInfo ? inmuebleInfo.querySelector('.text-sm')?.textContent || '' : '';
        
        const proyecto = row.querySelector('td:nth-child(2)')?.textContent || 'N/A';
        
        properties.push({
            proyecto: proyecto,
            inmueble: inmuebleNumero,
            detalle: inmuebleDetalle,
            precio_lista: precioLista,
            descuento: montoDescuento,
            precio_venta: precioVenta,
            separacion: separacion,
            cuota_inicial: cuotaInicial,
            saldo_financiar: saldoFinanciar
        });
        
        totalPrecioLista += precioLista;
        totalDescuento += montoDescuento;
        totalPrecioVenta += precioVenta;
        totalSeparacion += separacion;
        totalCuotaInicial += cuotaInicial;
        totalSaldoFinanciar += saldoFinanciar;
    });
    
    return {
        properties: properties,
        totals: {
            precio_lista: totalPrecioLista,
            descuento: totalDescuento,
            precio_venta: totalPrecioVenta,
            separacion: totalSeparacion,
            cuota_inicial: totalCuotaInicial,
            saldo_financiar: totalSaldoFinanciar
        },
        proforma_id: {{ $proformaId ?? 'null' }}
    };
}
</script>

@else
<div class="p-4 text-center text-gray-500">
    <p>Seleccione una proforma para ver los detalles de los inmuebles</p>
</div>
@endif