@php
    // Obtener los datos de la proforma seleccionada
    $proformaId = $this->data['proforma_id'] ?? null;
    $proforma = null;
    $inmueblesDisponibles = collect();
    $inmueblesConSeparacion = collect();
    
    if ($proformaId) {
        $proforma = \App\Models\Proforma::with([
            'departamento.proyecto', 
            'departamento.tipoInmueble', 
            'departamento.separaciones.proforma',
            // Usar siempre la relación a proforma_inmuebles
            'proformaInmuebles.departamento.proyecto', 
            'proformaInmuebles.departamento.tipoInmueble',
            'proformaInmuebles.departamento.separaciones.proforma'
        ])->find($proformaId);
        
        // Obtener todos los inmuebles disponibles de la proforma
        if ($proforma) {
            // Usar siempre los registros de proforma_inmuebles cuando existan
            if ($proforma->proformaInmuebles && $proforma->proformaInmuebles->count() > 0) {
                $inmueblesDisponibles = $proforma->proformaInmuebles;
            }
            // Si no existen registros (caso migraciones antiguas), intentar usar el principal
            elseif ($proforma->departamento) {
                $inmueblePrincipal = $proforma->inmueblePrincipal;
                if ($inmueblePrincipal) {
                    $inmueblesDisponibles = collect([$inmueblePrincipal]);
                } else {
                    // Ultimo recurso: crear objeto minimal usando Departamento, con valores por defecto
                    $inmueblesDisponibles = collect([
                        (object) [
                            'id' => $proforma->departamento->id,
                            'departamento' => $proforma->departamento,
                            'descuento' => 0,
                            'monto_separacion' => 0,
                            'monto_cuota_inicial' => 0,
                            'precio_lista' => $proforma->departamento->Precio_lista ?? 0,
                        ]
                    ]);
                }
            }
            
            // Identificar inmuebles que ya tienen separaciones
            foreach ($inmueblesDisponibles as $inmueble) {
                $departamento = $inmueble->departamento;
                $separacionExistente = $departamento->separaciones()
                    ->whereHas('proforma', function($query) use ($proforma) {
                        $query->where('numero_documento', $proforma->numero_documento);
                    })
                    ->first();
                    
                if ($separacionExistente) {
                    $inmueble->tiene_separacion = true;
                    $inmueble->separacion_id = $separacionExistente->id;
                    $inmueble->separacion_fecha = $separacionExistente->created_at;
                    $inmueblesConSeparacion->push($inmueble);
                } else {
                    $inmueble->tiene_separacion = false;
                }
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
                            $tieneSeparacion = $inmueble->tiene_separacion ?? false;
                        @endphp
                        <option value="{{ $inmuebleId }}" 
                                data-proyecto="{{ $departamento->proyecto->nombre ?? 'N/A' }}"
                                data-numero="{{ $departamento->num_departamento ?? 'N/A' }}"
                                data-tipo="{{ $departamento->tipoInmueble->nombre ?? '' }}"
                                data-dormitorios="{{ $departamento->num_dormitorios ?? 0 }}"
                                data-area="{{ $departamento->construida ?? 0 }}"
                                data-precio="{{ $inmueble->precio_lista ?? $departamento->Precio_lista ?? 0 }}"
                                data-descuento="{{ $inmueble->descuento ?? 0 }}"
                                data-separacion="{{ $inmueble->monto_separacion ?? 0 }}"
                                data-cuota-inicial="{{ $inmueble->monto_cuota_inicial ?? 0 }}"
                                data-tiene-separacion="{{ $tieneSeparacion ? 'true' : 'false' }}"
                                data-separacion-id="{{ $inmueble->separacion_id ?? '' }}"
                                class="{{ $tieneSeparacion ? 'bg-green-100 text-green-800' : '' }}">
                            {{ $departamento->proyecto->nombre ?? 'N/A' }} - 
                            {{ $departamento->num_departamento ?? 'N/A' }} 
                            ({{ $departamento->tipoInmueble->nombre ?? '' }} - {{ $departamento->num_dormitorios }} dorm.)
                            @if($tieneSeparacion)
                                ✓ CON SEPARACIÓN
                            @endif
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
                onclick="openSFModalWithSeparaciones()"
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

// SISTEMA DE PROTECCIÓN Y RESTAURACIÓN MEJORADO
console.log('🔍 Verificando disponibilidad de morphdom:', typeof window.morphdom);
console.log('🔍 Verificando disponibilidad de Livewire:', typeof window.Livewire);

// Almacenamiento global para propiedades cargadas
window.loadedProperties = window.loadedProperties || [];

// Función para respaldar propiedades actuales
function backupProperties() {
    const tbody = document.getElementById('properties-tbody');
    if (tbody) {
        const dynamicRows = tbody.querySelectorAll('[data-dynamic-row]');
        window.loadedProperties = Array.from(dynamicRows).map(row => ({
            html: row.outerHTML,
            propertyId: row.getAttribute('data-property-id')
        }));
        console.log('💾 Respaldadas', window.loadedProperties.length, 'propiedades');
    }
}

// Variable para evitar múltiples restauraciones simultáneas
window.restoringProperties = false;

// Función para restaurar propiedades si desaparecen
function restoreProperties() {
    const tbody = document.getElementById('properties-tbody');
    if (!tbody || window.restoringProperties) return;
    
    window.restoringProperties = true;
    
    // Verificar múltiples selectores para detectar filas dinámicas
    const currentDynamicRows = tbody.querySelectorAll('[data-dynamic-row], [data-property-id], [data-index]');
    const allRows = tbody.querySelectorAll('tr');
    
    console.log('🔍 Verificando estado de propiedades:', {
        loadedProperties: window.loadedProperties?.length || 0,
        currentDynamicRows: currentDynamicRows.length,
        allRows: allRows.length
    });
    
    // Solo restaurar si realmente no hay filas dinámicas Y tenemos propiedades respaldadas
    // Y no hay filas con data-index (que es lo que usamos para las propiedades)
    const hasDataIndexRows = tbody.querySelectorAll('[data-index]').length > 0;
    
    if (window.loadedProperties && window.loadedProperties.length > 0 && 
        currentDynamicRows.length === 0 && 
        !hasDataIndexRows &&
        allRows.length <= 1) { // Solo la fila de "No hay inmuebles" o vacío
        
        console.log('🚨 ¡Propiedades realmente desaparecieron! Restaurando', window.loadedProperties.length, 'propiedades');
        
        // Remover fila por defecto si existe
        const defaultRow = tbody.querySelector('tr:not([data-dynamic-row]):not([data-property-id]):not([data-index])');
        if (defaultRow) {
            console.log('🗑️ Removiendo fila por defecto');
            defaultRow.remove();
        }
        
        // Restaurar cada propiedad
        window.loadedProperties.forEach((property, index) => {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = property.html;
            const restoredRow = tempDiv.firstElementChild;
            
            // Asegurar que los atributos de protección estén establecidos
            restoredRow.setAttribute('data-dynamic-row', 'true');
            restoredRow.setAttribute('data-livewire-ignore', 'true');
            restoredRow.setAttribute('wire:ignore', 'true');
            
            tbody.appendChild(restoredRow);
            console.log(`✅ Propiedad ${index + 1} restaurada`);
        });
        
        console.log('✅ Propiedades restauradas exitosamente');
    } else {
        console.log('ℹ️ No es necesario restaurar propiedades - están presentes');
    }
    
    // Reset de la variable de control
    setTimeout(() => {
        window.restoringProperties = false;
    }, 2000);
}

// INTERCEPTOR DE MORPHDOM MEJORADO
if (window.morphdom) {
    const originalMorphdom = window.morphdom;
    window.morphdom = function(fromNode, toNode, options = {}) {
        // PROTECCIÓN: No interferir si el modal SF está activo
        if (window.sfModalActive) {
            console.log('🛡️ Modal SF activo - saltando morphdom');
            return fromNode; // Retornar el nodo original sin cambios
        }
        
        console.log('🔄 MORPHDOM EJECUTÁNDOSE:', fromNode, toNode);
        
        // Respaldar propiedades antes de que morphdom ejecute
        backupProperties();
        
        const originalOnBeforeElUpdated = options.onBeforeElUpdated;
        
        options.onBeforeElUpdated = function(fromEl, toEl) {
            // Proteger filas dinámicas con múltiples verificaciones
            if (fromEl.hasAttribute && (
                fromEl.hasAttribute('data-dynamic-row') ||
                fromEl.hasAttribute('data-property-id') ||
                fromEl.hasAttribute('data-livewire-ignore') ||
                fromEl.hasAttribute('wire:ignore')
            )) {
                console.log('🛡️ MORPHDOM INTERCEPTADO: Protegiendo fila dinámica', fromEl);
                return false; // Prevenir la actualización
            }
            
            // Proteger el tbody completo de propiedades
            if (fromEl.id === 'properties-tbody') {
                console.log('🛡️ MORPHDOM INTERCEPTADO: Protegiendo tbody de propiedades');
                return false;
            }
            
            // Si hay un callback original, ejecutarlo
            if (originalOnBeforeElUpdated) {
                return originalOnBeforeElUpdated(fromEl, toEl);
            }
            
            return true;
        };
        
        const result = originalMorphdom(fromNode, toNode, options);
        
        // Verificar y restaurar propiedades después de que morphdom ejecute (timeout más largo)
        setTimeout(restoreProperties, 500);
        
        return result;
    };
    console.log('🛡️ Interceptor de morphdom mejorado instalado con restauración automática');
} else {
    console.log('⚠️ morphdom no está disponible aún, intentando instalar interceptor más tarde...');
    
    // Intentar instalar el interceptor cuando morphdom esté disponible
    const checkMorphdom = setInterval(() => {
        if (window.morphdom) {
            console.log('✅ morphdom ahora disponible, instalando interceptor mejorado...');
            const originalMorphdom = window.morphdom;
            window.morphdom = function(fromNode, toNode, options = {}) {
                console.log('🔄 MORPHDOM EJECUTÁNDOSE (instalado tardíamente):', fromNode, toNode);
                
                // Respaldar propiedades antes de que morphdom ejecute
                backupProperties();
                
                const originalOnBeforeElUpdated = options.onBeforeElUpdated;
                
                options.onBeforeElUpdated = function(fromEl, toEl) {
                    // Proteger filas dinámicas con múltiples verificaciones
                    if (fromEl.hasAttribute && (
                        fromEl.hasAttribute('data-dynamic-row') ||
                        fromEl.hasAttribute('data-property-id') ||
                        fromEl.hasAttribute('data-livewire-ignore') ||
                        fromEl.hasAttribute('wire:ignore')
                    )) {
                        console.log('🛡️ MORPHDOM INTERCEPTADO (tardío): Protegiendo fila dinámica', fromEl);
                        return false;
                    }
                    
                    // Proteger el tbody completo de propiedades
                    if (fromEl.id === 'properties-tbody') {
                        console.log('🛡️ MORPHDOM INTERCEPTADO (tardío): Protegiendo tbody de propiedades');
                        return false;
                    }
                    
                    if (originalOnBeforeElUpdated) {
                        return originalOnBeforeElUpdated(fromEl, toEl);
                    }
                    
                    return true;
                };
                
                const result = originalMorphdom(fromNode, toNode, options);
                
                // Verificar y restaurar propiedades después de que morphdom ejecute (timeout más largo)
                setTimeout(restoreProperties, 500);
                
                return result;
            };
            console.log('🛡️ Interceptor de morphdom mejorado instalado tardíamente con restauración automática');
            clearInterval(checkMorphdom);
        }
    }, 100);
    
    // Limpiar el intervalo después de 10 segundos
    setTimeout(() => clearInterval(checkMorphdom), 10000);
}

// OBSERVADOR DE MUTACIONES para detectar cambios en el tbody
document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('properties-tbody');
    if (tbody) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    // Respaldar propiedades cuando se agregan
                    if (mutation.addedNodes.length > 0) {
                        setTimeout(backupProperties, 50);
                    }
                    
                    // Verificar si las propiedades fueron removidas y restaurar si es necesario (timeout más largo)
                    if (mutation.removedNodes.length > 0) {
                        setTimeout(restoreProperties, 1000);
                    }
                }
            });
        });
        
        observer.observe(tbody, {
            childList: true,
            subtree: true
        });
        
        console.log('👁️ Observador de mutaciones instalado para tbody de propiedades');
    }
});

// INTERCEPTOR ALTERNATIVO PARA LIVEWIRE
if (window.Livewire) {
    console.log('🔄 Instalando interceptor de Livewire...');
    
    // Interceptar eventos de Livewire que puedan causar re-renderizado
    window.Livewire.on('component.updated', (component) => {
        // PROTECCIÓN: No interferir si el modal SF está activo
        if (window.sfModalActive) {
            console.log('🛡️ Modal SF activo - saltando actualización de Livewire');
            return;
        }
        
        console.log('🔄 Componente Livewire actualizado:', component);
        
        // Restaurar filas dinámicas si fueron removidas
        setTimeout(() => {
            const tbody = document.getElementById('properties-tbody');
            if (tbody) {
                const dynamicRows = tbody.querySelectorAll('[data-dynamic-row="true"]');
                console.log('🔍 Filas dinámicas encontradas después de actualización:', dynamicRows.length);

                if (dynamicRows.length === 0 && addedProperties.length > 0) {
                    console.log('⚠️ Filas dinámicas perdidas, intentando restaurar...');
                    // Aquí podríamos restaurar las filas si es necesario
                }
            }
        }, 10);
    });
    
    console.log('✅ Interceptor de Livewire instalado con protección SF');
} else {
    console.log('⚠️ Livewire no está disponible aún...');
}

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
    
    // Interceptor para detectar modificaciones al tbody
    const tbody = document.getElementById('properties-tbody');
    if (tbody) {
        const originalInnerHTML = Object.getOwnPropertyDescriptor(Element.prototype, 'innerHTML');
        
        Object.defineProperty(tbody, 'innerHTML', {
            get: function() {
                return originalInnerHTML.get.call(this);
            },
            set: function(value) {
                // Solo log en modo debug si es necesario
                // console.log('🔄 innerHTML del tbody modificado');
                return originalInnerHTML.set.call(this, value);
            }
        });
         
         // Interceptor para detectar cuando se remueven elementos (solo alertas críticas)
          const originalRemoveChild = tbody.removeChild;
          tbody.removeChild = function(child) {
              // Solo alertar si es una fila de propiedad con datos importantes
              if (child.dataset && child.dataset.propertyId && child.dataset.separacionId) {
                  console.warn('⚠️ Removiendo fila con separación:', child.dataset.propertyId);
              }
              return originalRemoveChild.call(this, child);
          };
          
          // Interceptor para el método remove() en elementos hijos (solo alertas críticas)
          const observer = new MutationObserver(function(mutations) {
              mutations.forEach(function(mutation) {
                  if (mutation.type === 'childList' && mutation.removedNodes.length > 0) {
                      mutation.removedNodes.forEach(function(node) {
                          if (node.nodeType === 1 && node.tagName === 'TR' && node.dataset && node.dataset.propertyId && node.dataset.separacionId) {
                              console.warn('⚠️ Fila con separación removida:', node.dataset.propertyId);
                          }
                      });
                  }
              });
          });
          
          observer.observe(tbody, { childList: true, subtree: true });
           
           // Guardar el contenido inicial del tbody para comparar (modo silencioso)
           let lastTbodyContent = tbody.innerHTML;
           
           // Verificar cambios cada 500ms (reducido para menos ruido)
           const contentChecker = setInterval(() => {
               const currentContent = tbody.innerHTML;
               if (currentContent !== lastTbodyContent) {
                   // Solo log en modo debug si es necesario
                   // console.log('🔄 Contenido del tbody actualizado');
                   lastTbodyContent = currentContent;
               }
           }, 500);
     }
     
     // Cargar propiedades inmediatamente y configurar reintentos si es necesario
    let retryCount = 0;
    const maxRetries = 3;
    
    // Primera carga inmediata
    loadPropertiesFromAPI();
    
    // Verificar después de un momento si se cargaron correctamente
    setTimeout(() => {
        const tbody = document.getElementById('properties-tbody');
        const currentRows = tbody ? tbody.querySelectorAll('tr:not(#default-row)').length : 0;
        
        if (currentRows === 0) {
            console.log('🔄 Primera carga no exitosa, iniciando sistema de reintentos...');
            
            const retryInterval = setInterval(() => {
                const tbody = document.getElementById('properties-tbody');
                const currentRows = tbody ? tbody.querySelectorAll('tr:not(#default-row)').length : 0;
                
                if (currentRows === 0 && retryCount < maxRetries) {
                    retryCount++;
                    console.log(`🔄 Reintento ${retryCount}/${maxRetries} - Cargando propiedades...`);
                    loadPropertiesFromAPI();
                } else if (currentRows > 0 || retryCount >= maxRetries) {
                    clearInterval(retryInterval);
                    if (currentRows > 0) {
                        console.log('✅ Propiedades cargadas exitosamente');
                    } else {
                        console.log('⚠️ No se pudieron cargar propiedades después de varios intentos');
                    }
                }
            }, 2000);
        } else {
            console.log('✅ Propiedades cargadas exitosamente en el primer intento');
        }
    }, 1000);
});

// Función para cargar propiedades desde la API
async function loadPropertiesFromAPI() {
    const proformaId = {{ $proformaId ?? 'null' }};
    
    if (!proformaId) {
        console.log('❌ No hay proforma seleccionada');
        return;
    }
    
    try {
        console.log('🔍 Cargando propiedades desde la API...');
        const response = await fetch(`/api/propiedades-con-separacion?proforma_id=${proformaId}`);
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('📦 Datos recibidos de la API:', data);
        
        // Poblar el selector con las propiedades
        populatePropertySelector(data.propiedades);
        
        // Cargar automáticamente propiedades con separación existente
        if (data.propiedades && data.propiedades.length > 0) {
            const propiedadesConSeparacion = data.propiedades.filter(prop => prop.tiene_separacion);
            
            if (propiedadesConSeparacion.length > 0) {
                console.log('🔍 Cargando automáticamente propiedades con separaciones existentes...');
                
                // Limpiar completamente antes de cargar nuevas propiedades
                addedProperties.length = 0;
                
                // Limpiar la tabla de propiedades existentes
                const tbody = document.getElementById('properties-tbody');
                if (tbody) {
                    const existingRows = tbody.querySelectorAll('tr:not(#default-row):not(#totals-row)');
                    existingRows.forEach(row => row.remove());
                }
                
                // Rehabilitar todas las opciones del selector
                const selector = document.getElementById('property-selector');
                if (selector) {
                    Array.from(selector.options).forEach(option => {
                        if (option.value) {
                            option.disabled = false;
                            option.removeAttribute('data-auto-selected');
                        }
                    });
                }
                
                propiedadesConSeparacion.forEach((propiedad, index) => {
                    // Buscar la opción en el selector
                    const option = document.querySelector(`option[value="${propiedad.id}"]`);
                    if (option) {
                        // Marcar como seleccionado automáticamente
                        option.dataset.autoSelected = 'true';
                        
                        // Agregar automáticamente a la tabla
                        const propertyData = {
                            id: propiedad.id,
                            departamento_id: propiedad.id,
                            proyecto: propiedad.proyecto,
                            numero: propiedad.numero,
                            tipo: propiedad.tipo,
                            dormitorios: propiedad.dormitorios,
                            area: propiedad.area,
                            precio: propiedad.precio,
                            descuento: propiedad.descuento,
                            separacion: propiedad.separacion,
                            cuotaInicial: propiedad.cuota_inicial,
                            tieneSeparacion: true,
                            separacionId: propiedad.separacion_id || ''
                        };
                        
                        addPropertyToTable(propertyData);
                        addedProperties.push(propiedad.id.toString());
                        option.disabled = true;
                    }
                });
                
                // Actualizar la interfaz después de cargar propiedades automáticamente
                setTimeout(() => {
                    updateCalculations();
                    
                    const emptyMessage = document.getElementById('empty-message');
                    if (emptyMessage) {
                        emptyMessage.style.display = 'none';
                    }
                    
                    const totalsRow = document.getElementById('totals-row');
                    if (totalsRow) {
                        totalsRow.style.display = '';
                    }
                    
                    const defaultRow = document.getElementById('default-row');
                    if (defaultRow) {
                        defaultRow.style.display = 'none';
                    }
                    
                    // Verificar que las propiedades estén realmente visibles
                    const tbody = document.getElementById('properties-tbody');
                    const visibleRows = tbody ? tbody.querySelectorAll('tr:not(#default-row)').length : 0;
                    console.log(`✅ Propiedades cargadas y visibles: ${visibleRows}`);
                    
                    if (visibleRows === 0 && propiedadesConSeparacion.length > 0) {
                        console.log('⚠️ Las propiedades se cargaron pero no son visibles, forzando restauración...');
                        setTimeout(restoreProperties, 100);
                    }
                    
                    updateCalculations();
                    
                    // Log detallado después de updateCalculations
                    const rows = tbody ? tbody.querySelectorAll('tr:not(#default-row)') : [];
                    console.log('📊 Estado después de updateCalculations:');
                    console.log('  - Filas en tbody:', rows.length);
                    console.log('  - Propiedades agregadas:', addedProperties.length);
                    console.log('  - Lista de propiedades:', addedProperties);
                    
                    // Verificar cada fila individualmente
                    rows.forEach((row, index) => {
                        console.log(`  - Fila ${index}:`, {
                            propertyId: row.dataset.propertyId,
                            index: row.dataset.index,
                            visible: row.style.display !== 'none',
                            inDOM: document.contains(row)
                        });
                    });
                    
                    console.log('✅ Propiedades con separaciones cargadas automáticamente');
                    
                    // Verificación adicional después de un momento más
                    setTimeout(() => {
                        const tbody2 = document.getElementById('properties-tbody');
                        const rows2 = tbody2 ? tbody2.querySelectorAll('tr:not(#default-row)') : [];
                        console.log('🔍 VERIFICACIÓN FINAL (200ms después):');
                        console.log('  - Filas visibles:', rows2.length);
                        console.log('  - Propiedades en array:', addedProperties.length);
                        
                        if (addedProperties.length > 0 && rows2.length === 0) {
                            console.error('🚨 PROBLEMA CONFIRMADO: Las propiedades han desaparecido!');
                            console.log('🔧 Intentando identificar la causa...');
                            
                            // Verificar si el tbody existe
                            if (!tbody2) {
                                console.error('❌ El tbody ha sido eliminado del DOM');
                            } else {
                                console.log('✅ El tbody existe, pero las filas han sido removidas');
                                console.log('📋 🔍 Verificando estado de propiedades:', {
                                    loadedProperties: window.loadedProperties?.length || 0,
                                    currentDynamicRows: tbody2.querySelectorAll('[data-dynamic-row], [data-property-id], [data-index]').length,
                                    allRows: tbody2.querySelectorAll('tr').length
                                });
                                
                                // RESTAURAR INMEDIATAMENTE
                                restoreProperties();
                            }
                        }
                    }, 200);
                }, 100);
            }
        }
        
    } catch (error) {
        console.error('❌ Error al cargar propiedades:', error);
        const selector = document.getElementById('property-selector');
        if (selector) {
            selector.innerHTML = '<option value="">-- Error al cargar propiedades --</option>';
        }
    }
}

// Función para poblar el selector de propiedades
function populatePropertySelector(propiedades) {
    const selector = document.getElementById('property-selector');
    if (!selector) return;
    
    // Limpiar opciones existentes
    selector.innerHTML = '<option value="">-- Seleccione un inmueble --</option>';
    
    if (!propiedades || propiedades.length === 0) {
        selector.innerHTML = '<option value="">-- No hay propiedades disponibles --</option>';
        return;
    }
    
    // Agregar opciones de propiedades
    propiedades.forEach(propiedad => {
        const option = document.createElement('option');
        option.value = propiedad.id;
        
        // Crear texto descriptivo
        const separacionText = propiedad.tiene_separacion ? ' ✓ CON SEPARACIÓN' : '';
        option.textContent = `${propiedad.proyecto} - ${propiedad.numero} (${propiedad.tipo} - ${propiedad.dormitorios} dorm.)${separacionText}`;
        
        // Agregar datos como atributos
        option.dataset.departamentoId = propiedad.id;
        option.dataset.proyecto = propiedad.proyecto;
        option.dataset.numero = propiedad.numero;
        option.dataset.tipo = propiedad.tipo;
        option.dataset.dormitorios = propiedad.dormitorios;
        option.dataset.area = propiedad.area;
        option.dataset.precio = propiedad.precio;
        option.dataset.descuento = propiedad.descuento;
        option.dataset.separacion = propiedad.separacion;
        option.dataset.cuotaInicial = propiedad.cuota_inicial;
        option.dataset.tieneSeparacion = propiedad.tiene_separacion;
        option.dataset.separacionId = propiedad.separacion_id || '';
        
        selector.appendChild(option);
    });
    
    console.log('✅ Selector de propiedades poblado con', propiedades.length, 'propiedades');
}

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
        departamento_id: parseInt(selectedOption.dataset.departamentoId || selectedOption.value, 10),
        proyecto: selectedOption.dataset.proyecto,
        numero: selectedOption.dataset.numero,
        tipo: selectedOption.dataset.tipo,
        dormitorios: selectedOption.dataset.dormitorios,
        area: selectedOption.dataset.area,
        precio: parseFloat(selectedOption.dataset.precio),
        descuento: parseFloat(selectedOption.dataset.descuento),
        separacion: parseFloat(selectedOption.dataset.separacion),
        cuotaInicial: parseFloat(selectedOption.dataset.cuotaInicial),
        tieneSeparacion: selectedOption.dataset.tieneSeparacion === 'true',
        separacionId: selectedOption.dataset.separacionId || ''
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
    
    console.log('🔧 Agregando propiedad:', propertyData.id, 'Index:', index);
    console.log('📊 Número de filas antes:', tbody.children.length);
    console.log('📋 Contenido actual del tbody:', tbody.innerHTML.length > 0 ? 'Tiene contenido' : 'Vacío');
    
    // Calcular el monto del descuento basado en el porcentaje original
    const montoDescuento = propertyData.precio * (propertyData.descuento / 100);
    const precioVenta = propertyData.precio - montoDescuento;
    const saldoFinanciar = precioVenta - propertyData.separacion - propertyData.cuotaInicial;
    
    const row = document.createElement('tr');
    // Determinar el estilo de la fila basado en si tiene separación
    const baseClass = 'hover:bg-gray-50 property-row';
    const separacionClass = propertyData.tieneSeparacion ? ' bg-green-50 border-green-200' : '';
    row.className = baseClass + separacionClass;
    row.dataset.index = index;
    row.dataset.propertyId = propertyData.id;
    row.dataset.departamentoId = propertyData.departamento_id;
    
    // Crear badge de estado
    const statusBadge = propertyData.tieneSeparacion ? 
        '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 ml-2">✓ Con Separación</span>' : 
        '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ml-2">Nueva</span>';
    
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
            ${statusBadge}
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
    
    console.log('📝 HTML de la nueva fila generado correctamente');
    
    // PROTECCIÓN CONTRA MORPHDOM: Marcar la fila ANTES de agregarla al DOM
    row.setAttribute('data-dynamic-row', 'true');
    row.setAttribute('data-livewire-ignore', 'true');
    row.setAttribute('wire:ignore', 'true');
    console.log('🛡️ Fila protegida contra morphdom con atributos especiales');
    
    tbody.appendChild(row);
    
    console.log('✅ Fila agregada al tbody. Número de filas después:', tbody.children.length);
    console.log('🔍 Verificando que la fila esté en el DOM:', document.querySelector(`[data-index="${index}"]`) ? 'SÍ' : 'NO');
    
    // Verificar después de un momento si la fila sigue ahí
    setTimeout(() => {
        const stillThere = document.querySelector(`[data-index="${index}"]`);
        console.log(`⏰ Verificación después de 50ms - Fila ${index} sigue en DOM:`, stillThere ? 'SÍ' : 'NO');
        if (!stillThere) {
            console.error('❌ PROBLEMA: La fila fue removida del DOM después de agregarla');
        }
    }, 50);
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

// Función para obtener separaciones existentes sin crear nuevas
function getExistingSeparaciones(multipleData) {
    console.log('🔍 === OBTENIENDO SEPARACIONES EXISTENTES ===');
    console.log('📊 Datos a verificar:', multipleData);
    
    const existingSeparaciones = [];
    const needToCreate = [];
    
    for (const property of multipleData.properties) {
        const row = document.querySelector(`#properties-tbody tr[data-departamento-id="${property.departamento_id}"]`);
        if (row && row.dataset.separacionId) {
            console.log(`✅ Separación existente para departamento ${property.departamento_id}: ${row.dataset.separacionId}`);
            existingSeparaciones.push({
                departamento_id: property.departamento_id,
                id: row.dataset.separacionId,
                property: property
            });
        } else {
            console.log(`⚠️ Separación no existe para departamento ${property.departamento_id}`);
            needToCreate.push(property);
        }
    }
    
    return {
        existing: existingSeparaciones,
        needToCreate: needToCreate,
        allExist: needToCreate.length === 0
    };
}

// Funciones para abrir los modales de cronograma
window.openCronogramaModal = async function() {
    console.log('🔍 === INICIO openCronogramaModal ===');
    
    try {
        console.log('🔍 Iniciando openCronogramaModal()');
        console.log('🔍 Verificando funciones disponibles:', {
            getMultiplePropertiesData: typeof getMultiplePropertiesData,
            createMultipleSeparaciones: typeof createMultipleSeparaciones
        });
        
        // Verificar si las funciones existen antes de usarlas
        if (typeof getMultiplePropertiesData !== 'function') {
            console.error('❌ getMultiplePropertiesData no está disponible');
            // Abrir modal directamente sin disparar eventos
            const modal = document.getElementById('cronograma-modal');
            if (modal) modal.classList.remove('hidden');
            return;
        }
        
        if (typeof createMultipleSeparaciones !== 'function') {
            console.error('❌ createMultipleSeparaciones no está disponible');
            // Abrir modal directamente sin disparar eventos
            const modal = document.getElementById('cronograma-modal');
            if (modal) modal.classList.remove('hidden');
            return;
        }
        
        console.log('🔍 Llamando a getMultiplePropertiesData()...');
        // Obtener los datos de múltiples propiedades
        const multipleData = getMultiplePropertiesData();
        console.log('📊 Datos de múltiples propiedades generados:', multipleData);
        
        console.log('🔍 Verificando si multipleData es válido...');
        if (!multipleData || !multipleData.properties || multipleData.properties.length === 0) {
            console.warn('⚠️ No hay datos de múltiples propiedades válidos:', multipleData);
            console.log('🔍 Abriendo modal sin datos múltiples...');
            // Abrir modal directamente sin disparar eventos
            const modal = document.getElementById('cronograma-modal');
            if (modal) modal.classList.remove('hidden');
            return;
        }
        
        // ESTABLECER DATOS INMEDIATAMENTE Y DE MÚLTIPLES FORMAS
        window.multiplePropertiesData = multipleData;
        window.globalMultipleData = multipleData;
        window.backupMultipleData = JSON.parse(JSON.stringify(multipleData)); // Deep copy
        
        // Almacenar en sessionStorage
        try {
            sessionStorage.setItem('multiplePropertiesData', JSON.stringify(multipleData));
            sessionStorage.setItem('globalMultipleData', JSON.stringify(multipleData));
            console.log('✅ Datos guardados en sessionStorage');
        } catch (e) {
            console.warn('⚠️ No se pudo guardar en sessionStorage:', e);
        }
        
        // Almacenar en localStorage como último recurso
        try {
            localStorage.setItem('tempMultiplePropertiesData', JSON.stringify(multipleData));
            console.log('✅ Datos guardados en localStorage');
        } catch (e) {
            console.warn('⚠️ No se pudo guardar en localStorage:', e);
        }
        
        console.log('✅ Datos establecidos en múltiples ubicaciones');
        console.log('🔍 window.multiplePropertiesData:', window.multiplePropertiesData);
        console.log('🔍 window.globalMultipleData:', window.globalMultipleData);
        
        // Verificar separaciones existentes - SOLO usar las que ya existen, NO crear nuevas
        try {
            console.log('🔍 Verificando separaciones existentes...');
            const separacionesStatus = getExistingSeparaciones(multipleData);
            
            if (separacionesStatus.allExist) {
                console.log('✅ Todas las separaciones ya existen, usando IDs existentes');
                // Asignar los IDs existentes a los datos
                multipleData.separaciones = separacionesStatus.existing;
            } else {
                console.log(`⚠️ Faltan ${separacionesStatus.needToCreate.length} separaciones de ${multipleData.properties.length} total`);
                console.log('📋 Para acceder al cronograma de cuota inicial, primero debe crear las separaciones desde los otros modales');
                
                // Solo asignar las separaciones que SÍ existen (si las hay)
                if (separacionesStatus.existing.length > 0) {
                    multipleData.separaciones = separacionesStatus.existing;
                    console.log(`✅ Usando ${separacionesStatus.existing.length} separaciones existentes`);
                } else {
                    console.log('⚠️ No hay separaciones existentes para mostrar en el cronograma');
                }
            }
        } catch (separacionError) {
            console.warn('⚠️ Error al verificar separaciones, pero continuando con el modal:', separacionError);
        }
        
        // Cargar cuotas existentes para las propiedades
        await loadExistingCuotasForMultipleProperties(multipleData);
        
        // VERIFICAR Y RESTAURAR DATOS ANTES DE ABRIR EL MODAL
        const verifyAndRestoreData = () => {
            console.log('🔍 Verificando datos antes de abrir modal...');
            
            if (!window.multiplePropertiesData) {
                console.log('⚠️ window.multiplePropertiesData perdido, restaurando...');
                
                // Intentar desde backup en memoria
                if (window.globalMultipleData) {
                    window.multiplePropertiesData = window.globalMultipleData;
                    console.log('🔄 Restaurado desde window.globalMultipleData');
                } else if (window.backupMultipleData) {
                    window.multiplePropertiesData = window.backupMultipleData;
                    window.globalMultipleData = window.backupMultipleData;
                    console.log('🔄 Restaurado desde window.backupMultipleData');
                } else {
                    // Intentar desde sessionStorage
                    try {
                        const stored = sessionStorage.getItem('multiplePropertiesData');
                        if (stored) {
                            const parsedData = JSON.parse(stored);
                            window.multiplePropertiesData = parsedData;
                            window.globalMultipleData = parsedData;
                            console.log('🔄 Restaurado desde sessionStorage');
                        }
                    } catch (e) {
                        // Intentar desde localStorage
                        try {
                            const stored = localStorage.getItem('tempMultiplePropertiesData');
                            if (stored) {
                                const parsedData = JSON.parse(stored);
                                window.multiplePropertiesData = parsedData;
                                window.globalMultipleData = parsedData;
                                console.log('🔄 Restaurado desde localStorage');
                            }
                        } catch (e2) {
                            console.error('❌ No se pudo restaurar datos desde ninguna fuente');
                        }
                    }
                }
            }
            
            console.log('🔍 Estado final antes de abrir modal:', {
                multiplePropertiesData: window.multiplePropertiesData,
                globalMultipleData: window.globalMultipleData,
                hasData: !!(window.multiplePropertiesData && window.multiplePropertiesData.totals)
            });
        };
        
        // Verificar inmediatamente
        verifyAndRestoreData();
        
        // Abrir el modal con un delay mínimo
        setTimeout(() => {
            // Verificar una vez más antes de abrir
            verifyAndRestoreData();
            
            console.log('🚀 Abriendo modal del cronograma de cuota inicial directamente');
            // Abrir modal directamente sin disparar eventos
            const modal = document.getElementById('cronograma-modal');
            if (modal) {
                modal.classList.remove('hidden');
                // Llamar a la función de mostrar modal si existe
                if (typeof window.showCronogramaModal === 'function') {
                    window.showCronogramaModal();
                }
            }
        }, 100); // Delay reducido a 100ms
        
    } catch (error) {
        console.error('❌ Error en openCronogramaModal:', error);
        console.error('❌ Stack trace:', error.stack);
        alert('Error al procesar los datos. Por favor, intente nuevamente.');
    }
}

// Función para abrir modal de cronograma para una propiedad específica
function openCronogramaModalForProperty(propertyId) {
    console.log('🔍 Abriendo modal de cronograma para propiedad:', propertyId);
    
    // Verificar si multiplePropertiesData existe
    if (!window.multiplePropertiesData) {
        console.log('⚠️ multiplePropertiesData no existe, obteniendo datos...');
        window.multiplePropertiesData = getMultiplePropertiesData();
    }
    
    // Buscar la propiedad específica
    const property = window.multiplePropertiesData.properties.find(p => p.id == propertyId);
    if (!property) {
        console.error('❌ No se encontró la propiedad:', propertyId);
        alert('Error: No se encontró la información de la propiedad');
        return;
    }
    
    console.log('📋 Datos de la propiedad encontrada:', property);
    
    // Establecer datos globales para el modal
    window.currentProformaId = window.multiplePropertiesData.proforma_id;
    window.currentPropertyId = propertyId;
    window.currentSeparacionId = property.separacion_id || null;
    window.currentMontoTotal = property.cuota_inicial;
    
    // Cargar cuotas existentes para esta propiedad específica
    loadExistingCuotasForProperty(property);
    
    // Abrir el modal
    const modal = document.getElementById('cronograma-modal');
    if (modal) {
        modal.classList.remove('hidden');
        
        // Establecer valores en el formulario
        const montoTotalInput = document.getElementById('montoTotal');
        const fechaInicioInput = document.getElementById('fechaInicio');
        const numeroCuotasInput = document.getElementById('numeroCuotas');
        
        if (montoTotalInput) montoTotalInput.value = property.cuota_inicial;
        if (fechaInicioInput) fechaInicioInput.value = new Date().toISOString().split('T')[0];
        if (numeroCuotasInput) numeroCuotasInput.value = 1;
        
        // Disparar evento personalizado para mostrar cuotas
        setTimeout(() => {
            const event = new CustomEvent('modal-opened', {
                detail: { modalId: 'cronograma-modal', propertyId: propertyId }
            });
            document.dispatchEvent(event);
            
            // Mostrar cuotas existentes si las hay
            displayExistingCuotasInModal();
        }, 100);
        
        console.log('✅ Modal de cronograma abierto');
    } else {
        console.error('❌ No se encontró el modal de cronograma');
    }
}

// Función para cargar cuotas existentes de una propiedad específica
async function loadExistingCuotasForProperty(property) {
    console.log('🔍 Cargando cuotas existentes para propiedad:', property);
    
    try {
        // Si la propiedad ya tiene separación, cargar cuotas definitivas
        if (property.tiene_separacion && property.separacion_id) {
            console.log('✅ Propiedad con separación existente:', property.separacion_id);
            
            const response = await fetch(`/cronograma/${property.separacion_id}`);
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.data && data.data.length > 0) {
                    console.log('✅ Cuotas definitivas encontradas:', data.data.length);
                    window.currentPropertyCuotas = data.data;
                    window.currentPropertyCuotasType = 'Definitivas';
                    return;
                }
            }
        }
        
        // Si no hay cuotas definitivas, buscar temporales por proforma_id
        const proformaId = window.multiplePropertiesData ? window.multiplePropertiesData.proforma_id : null;
        if (proformaId) {
            console.log('ℹ️ Buscando cuotas temporales para proforma:', proformaId);
            const tempResponse = await fetch(`/cronograma/temporales/${proformaId}`);
            
            if (tempResponse.ok) {
                const tempData = await tempResponse.json();
                if (tempData.success && tempData.data && tempData.data.length > 0) {
                    console.log('✅ Cuotas temporales encontradas:', tempData.data.length);
                    window.currentPropertyCuotas = tempData.data;
                    window.currentPropertyCuotasType = 'Temporales';
                    return;
                }
            }
        }
        
        console.log('ℹ️ No hay cuotas existentes para esta propiedad');
        window.currentPropertyCuotas = null;
        window.currentPropertyCuotasType = null;
        
    } catch (error) {
        console.error('❌ Error al cargar cuotas existentes:', error);
        window.currentPropertyCuotas = null;
        window.currentPropertyCuotasType = null;
    }
}

// Función para cargar cuotas existentes para múltiples propiedades
async function loadExistingCuotasForMultipleProperties(multipleData) {
    console.log('🔍 Cargando cuotas existentes para múltiples propiedades...');
    
    if (!multipleData || !multipleData.proforma_id) {
        console.log('⚠️ No hay proforma_id para cargar cuotas');
        return;
    }
    
    try {
        // Intentar cargar cuotas existentes para la proforma
        const response = await fetch(`/cronograma/proforma/${multipleData.proforma_id}`);
        
        if (response.ok) {
            const data = await response.json();
            
            if (data.success && data.data && data.data.length > 0) {
                console.log('✅ Cuotas existentes encontradas para la proforma:', data.data.length);
                window.existingCuotasData = data.data;
                return;
            }
        }
        
        // Si no hay cuotas definitivas, buscar temporales
        console.log('ℹ️ No hay cuotas definitivas, buscando temporales...');
        const tempResponse = await fetch(`/cronograma/temporales/${multipleData.proforma_id}`);
        
        if (tempResponse.ok) {
            const tempData = await tempResponse.json();
            
            if (tempData.success && tempData.data && tempData.data.length > 0) {
                console.log('✅ Cuotas temporales encontradas:', tempData.data.length);
                window.existingCuotasData = tempData.data;
                window.existingCuotasType = 'Temporales';
                return;
            }
        }
        
        console.log('ℹ️ No hay cuotas existentes para esta proforma');
        window.existingCuotasData = null;
        
    } catch (error) {
        console.error('❌ Error al cargar cuotas existentes:', error);
        window.existingCuotasData = null;
    }
}

async function openSFModalWithSeparaciones() {
    try {
        console.log('🚀 === INICIANDO openSFModalWithSeparaciones ===');
        
        // Crear separaciones para las propiedades seleccionadas
        const multipleData = getMultiplePropertiesData();
        console.log('📊 Datos múltiples obtenidos:', multipleData);
        
        await createMultipleSeparaciones(multipleData);
        console.log('✅ Separaciones creadas exitosamente');
        
        // Establecer los datos de múltiples propiedades para el cronograma SF
        window.multiplePropertiesData = multipleData;
        console.log('💾 window.multiplePropertiesData establecido:', window.multiplePropertiesData);
        
        // Verificar que los datos estén correctamente establecidos
        if (window.multiplePropertiesData && window.multiplePropertiesData.properties) {
            console.log('✅ Datos múltiples confirmados - propiedades:', window.multiplePropertiesData.properties.length);
        } else {
            console.error('❌ Error: window.multiplePropertiesData no está correctamente establecido');
        }
        
        // Abrir el modal directamente para asegurar que los datos múltiples estén disponibles
        console.log('🔄 Abriendo modal directamente para garantizar datos múltiples');
        const modal = document.getElementById('cronograma-sf-modal');
        if (modal) {
            // Activar protección manualmente
            window.sfModalActive = true;
            console.log('🛡️ Modal SF activado - protección habilitada');
            
            modal.classList.remove('hidden');
            
            // Cargar datos del modal con un pequeño delay para asegurar que los datos estén establecidos
            setTimeout(() => {
                console.log('🔄 Iniciando carga de datos del modal SF...');
                
                // Verificar nuevamente que los datos múltiples estén disponibles
                if (window.multiplePropertiesData && window.multiplePropertiesData.properties) {
                    console.log('✅ Datos múltiples disponibles, cargando...');
                    if (typeof loadMultipleSFData === 'function') {
                        loadMultipleSFData();
                    }
                } else {
                    console.log('⚠️ Datos múltiples no disponibles, cargando datos individuales');
                    if (typeof loadProformaSFData === 'function') {
                        loadProformaSFData();
                    }
                }
                
                // Cargar funciones adicionales
                if (typeof loadExistingCuotasSF === 'function') {
                    loadExistingCuotasSF();
                }
                if (typeof setDefaultSFDate === 'function') {
                    setDefaultSFDate();
                }
                if (typeof loadBancos === 'function') {
                    loadBancos();
                }
                if (typeof loadTiposFinanciamiento === 'function') {
                    loadTiposFinanciamiento();
                }
                if (typeof loadTiposComprobante === 'function') {
                    loadTiposComprobante();
                }
            }, 200);
        }
        
        console.log('✅ === FIN openSFModalWithSeparaciones ===');
    } catch (error) {
        console.error('❌ Error al crear separaciones:', error);
        alert('Error al procesar las separaciones. Por favor, intente nuevamente.');
    }
}

async function openPagoSeparacionModal() {
    try {
        // Crear separaciones para las propiedades seleccionadas
        const multipleData = getMultiplePropertiesData();
        await createMultipleSeparaciones(multipleData);
        
        // Establecer los datos de múltiples propiedades para el registro de pagos
        window.multiplePropertiesData = multipleData;
        
        // Abrir el modal directamente sin disparar evento para evitar bucle infinito
        const modal = document.getElementById('pago-separacion-modal');
        if (modal) {
            modal.classList.remove('hidden');
            // Cargar datos del modal si existe la función
            if (typeof loadPagoSeparacionData === 'function') {
                setTimeout(() => {
                    loadPagoSeparacionData();
                }, 200);
            }
        }
    } catch (error) {
        console.error('Error al crear separaciones:', error);
        alert('Error al procesar las separaciones. Por favor, intente nuevamente.');
    }
}

// Función para crear separaciones múltiples
async function createMultipleSeparaciones(multipleData) {
    console.log('🔄 === INICIANDO createMultipleSeparaciones ===');
    console.log('📊 Datos a enviar:', JSON.stringify(multipleData, null, 2));
    
    try {
        // Verificar si las separaciones ya existen
        console.log('🔍 Verificando separaciones existentes...');
        const existingSeparaciones = [];
        const needToCreate = [];
        
        for (const property of multipleData.properties) {
            const row = document.querySelector(`#properties-tbody tr[data-departamento-id="${property.departamento_id}"]`);
            if (row && row.dataset.separacionId) {
                console.log(`✅ Separación ya existe para departamento ${property.departamento_id}: ${row.dataset.separacionId}`);
                existingSeparaciones.push({
                    departamento_id: property.departamento_id,
                    id: row.dataset.separacionId
                });
            } else {
                console.log(`⚠️ Separación no existe para departamento ${property.departamento_id}, necesita crearse`);
                needToCreate.push(property);
            }
        }
        
        // Si todas las separaciones ya existen, no hacer nada
        if (needToCreate.length === 0) {
            console.log('✅ Todas las separaciones ya existen, no es necesario crear nuevas');
            return { success: true, message: 'Separaciones ya existentes', data: { separaciones: existingSeparaciones } };
        }
        
        console.log(`🔄 Necesario crear ${needToCreate.length} separaciones de ${multipleData.properties.length} total`);
        
        // Transformar los datos al formato esperado por el backend (solo las que necesitan crearse)
        const transformedData = {
            propiedades: needToCreate.map(property => ({
                        departamento_id: property.departamento_id,
                        precio_lista: property.precio_lista,
                        precio_venta: property.precio_venta,
                        monto_separacion: property.separacion,
                        monto_cuota_inicial: property.cuota_inicial,
                        saldo_financiar: property.saldo_financiar
                    })),
            cliente_data: {
                nombres: "{{ $proforma->nombres ?? '' }}",
                ape_paterno: "{{ $proforma->ape_paterno ?? '' }}",
                ape_materno: "{{ $proforma->ape_materno ?? '' }}",
                numero_documento: "{{ $proforma->numero_documento ?? '' }}",
                celular: "{{ $proforma->celular ?? '' }}",
                email: "{{ $proforma->email ?? '' }}"
            },
            proforma_id: {{ $proformaId ?? 'null' }}
        };
        
        console.log('🔄 Datos transformados:', JSON.stringify(transformedData, null, 2));
        
        const response = await fetch('/separaciones/multiple', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(transformedData)
        });
        
        console.log('📡 Respuesta del servidor - Status:', response.status);
        
        if (!response.ok) {
            const errorData = await response.json();
            console.error('❌ Error en la respuesta:', errorData);
            throw new Error(errorData.message || 'Error al crear separaciones');
        }
        
        const result = await response.json();
        console.log('✅ Separaciones creadas exitosamente:', result);

        // PROTECCIÓN: No modificar directamente la tabla de propiedades para evitar interferencia
        // Solo almacenar los datos de separaciones para uso posterior
        const separaciones = (result && result.data && Array.isArray(result.data.separaciones))
            ? result.data.separaciones
            : (result && Array.isArray(result.separaciones) ? result.separaciones : []);
        
        if (separaciones && separaciones.length) {
            // Almacenar los datos de separaciones sin modificar el DOM de la tabla
            window.separacionesData = window.separacionesData || {};
            separaciones.forEach(sep => {
                window.separacionesData[sep.departamento_id] = {
                    id: sep.id,
                    departamento_id: sep.departamento_id,
                    created: true
                };
                console.log(`💾 Separación almacenada para departamento ${sep.departamento_id}: ${sep.id}`);
            });
            
            console.log('💾 Datos de separaciones almacenados sin modificar tabla principal');
        }

        console.log('✅ === FIN createMultipleSeparaciones ===');
        return result;
    } catch (error) {
        console.error('❌ Error en createMultipleSeparaciones:', error);
        throw error;
    }
}

// Función para obtener los datos de múltiples propiedades
function getMultiplePropertiesData() {
    console.log('🔍 === INICIANDO getMultiplePropertiesData ===');
    
    // Verificar si existe el tbody
    const tbody = document.getElementById('properties-tbody');
    console.log('📋 Tbody encontrado:', tbody ? 'SÍ' : 'NO');
    
    if (tbody) {
        console.log('📋 Contenido del tbody:', tbody.innerHTML.substring(0, 500) + '...');
        console.log('📋 Número total de filas en tbody:', tbody.children.length);
    }
    
    // Buscar filas con múltiples selectores en orden de prioridad
    let rows = [];
    
    // 1. Intentar con data-index (selector original)
    const dataIndexRows = document.querySelectorAll('#properties-tbody tr[data-index]');
    console.log('📊 Filas encontradas con data-index:', dataIndexRows.length);
    
    // 2. Intentar con data-departamento-id (usado en las filas creadas)
    const departamentoIdRows = document.querySelectorAll('#properties-tbody tr[data-departamento-id]');
    console.log('📊 Filas encontradas con data-departamento-id:', departamentoIdRows.length);
    
    // 3. Intentar con data-property-id
    const propertyIdRows = document.querySelectorAll('#properties-tbody tr[data-property-id]');
    console.log('📊 Filas encontradas con data-property-id:', propertyIdRows.length);
    
    // 4. Intentar con data-dynamic-row (filas protegidas)
    const dynamicRows = document.querySelectorAll('#properties-tbody tr[data-dynamic-row]');
    console.log('📊 Filas encontradas con data-dynamic-row:', dynamicRows.length);
    
    // 5. Todas las filas excluyendo la por defecto
    const allNonDefaultRows = document.querySelectorAll('#properties-tbody tr:not(#default-row)');
    console.log('📊 Filas encontradas excluyendo default-row:', allNonDefaultRows.length);
    
    // Seleccionar el mejor conjunto de filas
    if (dataIndexRows.length > 0) {
        rows = dataIndexRows;
        console.log('✅ Usando filas con data-index');
    } else if (departamentoIdRows.length > 0) {
        rows = departamentoIdRows;
        console.log('✅ Usando filas con data-departamento-id');
    } else if (propertyIdRows.length > 0) {
        rows = propertyIdRows;
        console.log('✅ Usando filas con data-property-id');
    } else if (dynamicRows.length > 0) {
        rows = dynamicRows;
        console.log('✅ Usando filas con data-dynamic-row');
    } else if (allNonDefaultRows.length > 0) {
        rows = allNonDefaultRows;
        console.log('✅ Usando todas las filas no-default');
    } else {
        console.log('❌ No se encontraron filas válidas');
        
        // Debug adicional: mostrar todas las filas del tbody
        if (tbody) {
            console.log('🔍 DEBUG: Todas las filas en tbody:');
            Array.from(tbody.children).forEach((row, index) => {
                console.log(`  Fila ${index}:`, {
                    id: row.id,
                    className: row.className,
                    dataIndex: row.dataset.index,
                    dataDepartamentoId: row.dataset.departamentoId,
                    dataPropertyId: row.dataset.propertyId,
                    dataDynamicRow: row.dataset.dynamicRow,
                    innerHTML: row.innerHTML.substring(0, 100) + '...'
                });
            });
        }
        
        return null;
    }
    
    console.log(`🎯 Procesando ${rows.length} filas encontradas`);
    
    // Debug: mostrar información de cada fila encontrada
    Array.from(rows).forEach((row, index) => {
        console.log(`🔍 Fila ${index}:`, {
            id: row.id,
            dataIndex: row.dataset.index,
            dataDepartamentoId: row.dataset.departamentoId,
            dataPropertyId: row.dataset.propertyId,
            dataDynamicRow: row.dataset.dynamicRow
        });
    });
    
    const properties = [];
    let totalPrecioLista = 0;
    let totalDescuento = 0;
    let totalPrecioVenta = 0;
    let totalSeparacion = 0;
    let totalCuotaInicial = 0;
    let totalSaldoFinanciar = 0;
    
    rows.forEach((row, index) => {
        console.log(`🏠 Procesando fila ${index + 1}:`);
        
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
        const inmuebleNumero = inmuebleInfo ? (inmuebleInfo.querySelector('.font-medium')?.textContent || 'N/A').trim().replace(/\s+/g, ' ') : 'N/A';
        const inmuebleDetalle = inmuebleInfo ? (inmuebleInfo.querySelector('.text-sm')?.textContent || '').trim().replace(/\s+/g, ' ') : '';
        
        const proyecto = (row.querySelector('td:nth-child(2)')?.textContent || 'N/A').trim().replace(/\s+/g, ' ');
        const departamentoId = row.dataset.departamentoId || row.dataset.propertyId || row.dataset.index || null;
        
        console.log(`   - Departamento ID obtenido: ${departamentoId} (de ${row.dataset.departamentoId ? 'departamentoId' : row.dataset.propertyId ? 'propertyId' : row.dataset.index ? 'index' : 'ninguno'})`);
        
        const propertyData = {
            departamento_id: departamentoId ? parseInt(departamentoId, 10) : null,
            proyecto: proyecto,
            inmueble: inmuebleNumero,
            detalle: inmuebleDetalle,
            precio_lista: precioLista,
            descuento: montoDescuento,
            precio_venta: precioVenta,
            separacion: separacion,
            cuota_inicial: cuotaInicial,
            saldo_financiar: saldoFinanciar
        };
        
        console.log(`   - Proyecto: ${proyecto}`);
        console.log(`   - Inmueble: ${inmuebleNumero}`);
        console.log(`   - Precio Venta: ${precioVenta}`);
        console.log(`   - Cuota Inicial: ${cuotaInicial}`);
        
        properties.push(propertyData);
        
        totalPrecioLista += precioLista;
        totalDescuento += montoDescuento;
        totalPrecioVenta += precioVenta;
        totalSeparacion += separacion;
        totalCuotaInicial += cuotaInicial;
        totalSaldoFinanciar += saldoFinanciar;
    });
    
    const result = {
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
    
    console.log('📋 Datos generados:', JSON.stringify(result, null, 2));
    
    // Establecer la variable global automáticamente
    if (result && result.properties && result.properties.length > 0) {
        window.multiplePropertiesData = result;
        window.globalMultipleData = result; // Backup adicional
        console.log('✅ window.multiplePropertiesData establecido automáticamente');
        
        // También guardar en sessionStorage como backup
        try {
            sessionStorage.setItem('multiplePropertiesData', JSON.stringify(result));
            console.log('✅ Datos guardados en sessionStorage');
        } catch (e) {
            console.warn('⚠️ No se pudo guardar en sessionStorage:', e);
        }
    } else {
        console.log('⚠️ No se estableció window.multiplePropertiesData porque no hay propiedades válidas');
    }
    
    console.log('✅ === FIN getMultiplePropertiesData ===');
    
    return result;
}

// Función para mostrar cuotas existentes en el modal múltiple
function displayExistingCuotasInMultipleModal() {
    console.log('📋 Mostrando cuotas existentes en modal múltiple...');
    
    if (!window.existingCuotasData || window.existingCuotasData.length === 0) {
        console.log('ℹ️ No hay cuotas existentes para mostrar');
        hideExistingCuotasSection();
        return;
    }
    
    const cuotas = window.existingCuotasData;
    const tipo = window.existingCuotasType || 'Definitivas';
    
    // Buscar o crear sección de cuotas existentes
    let existingSection = document.getElementById('existing-cuotas-section');
    const modalContent = document.querySelector('#cronograma-modal .bg-white');
    
    if (!existingSection && modalContent) {
        const sectionHTML = `
            <div id="existing-cuotas-section" class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center mb-3">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <h3 id="existing-cuotas-title" class="text-lg font-medium text-blue-900">Cuotas Existentes</h3>
                </div>
                <p class="text-sm text-blue-700 mb-3">
                    Se encontraron cuotas existentes para esta proforma. Puede modificarlas o agregar nuevas cuotas.
                </p>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-left">N°</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Fecha Pago</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Monto</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Tipo</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Estado</th>
                            </tr>
                        </thead>
                        <tbody id="existing-cuotas-table-body">
                        </tbody>
                    </table>
                </div>
            </div>
        `;
        
        // Insertar antes del formulario de cronograma
        const cronogramaForm = modalContent.querySelector('.space-y-4');
        if (cronogramaForm) {
            cronogramaForm.insertAdjacentHTML('beforebegin', sectionHTML);
            existingSection = document.getElementById('existing-cuotas-section');
        }
    }
    
    if (!existingSection) {
        console.error('❌ No se pudo crear la sección de cuotas existentes');
        return;
    }
    
    // Actualizar título
    const title = document.getElementById('existing-cuotas-title');
    if (title) {
        title.textContent = `Cuotas Existentes (${tipo})`;
    }
    
    // Llenar tabla
    const tableBody = document.getElementById('existing-cuotas-table-body');
    if (tableBody) {
        tableBody.innerHTML = '';
        
        cuotas.forEach((cuota, index) => {
            const estadoClass = cuota.estado === 'Pagado' ? 'bg-green-100 text-green-800' : 
                               cuota.estado === 'Pendiente' ? 'bg-yellow-100 text-yellow-800' : 
                               'bg-red-100 text-red-800';
            
            const row = document.createElement('tr');
            row.className = 'border-b border-gray-200 hover:bg-gray-50';
            row.innerHTML = `
                <td class="px-4 py-2 text-sm">${index + 1}</td>
                <td class="px-4 py-2 text-sm">${new Date(cuota.fecha_pago).toLocaleDateString('es-PE')}</td>
                <td class="px-4 py-2 text-sm font-medium">S/ ${parseFloat(cuota.monto).toLocaleString('es-PE', {minimumFractionDigits: 2})}</td>
                <td class="px-4 py-2 text-sm">${cuota.tipo || cuota.tipo_cuota || 'Cuota Inicial'}</td>
                <td class="px-4 py-2">
                    <span class="px-2 py-1 text-xs font-medium rounded-full ${estadoClass}">
                        ${cuota.estado || 'Pendiente'}
                    </span>
                </td>
            `;
            tableBody.appendChild(row);
        });
    }
    
    // Mostrar sección
    existingSection.classList.remove('hidden');
    console.log('✅ Cuotas existentes mostradas en el modal múltiple');
}

// Función para ocultar sección de cuotas existentes
function hideExistingCuotasSection() {
    const existingSection = document.getElementById('existing-cuotas-section');
    if (existingSection) {
        existingSection.classList.add('hidden');
    }
}

// Event listener para mostrar cuotas cuando se abre el modal
document.addEventListener('DOMContentLoaded', function() {
    // Escuchar cuando se abre el modal de cronograma
    document.addEventListener('modal-opened', function(event) {
        if (event.detail && event.detail.modalId === 'cronograma-modal') {
            console.log('🔍 Modal de cronograma abierto, mostrando cuotas existentes...');
            setTimeout(() => {
                displayExistingCuotasInMultipleModal();
            }, 200);
        }
    });
});

// Log de confirmación de carga del script
console.log('🚀 Script propiedades-tabla-separacion.blade.php cargado correctamente');
console.log('🔍 Funciones disponibles:', {
    openCronogramaModal: typeof window.openCronogramaModal,
    getMultiplePropertiesData: typeof getMultiplePropertiesData,
    createMultipleSeparaciones: typeof createMultipleSeparaciones,
    displayExistingCuotasInMultipleModal: typeof displayExistingCuotasInMultipleModal,
    loadExistingCuotasForMultipleProperties: typeof loadExistingCuotasForMultipleProperties
});
</script>

@else
<div class="p-4 text-center text-gray-500">
    <p>Seleccione una proforma para ver los detalles de los inmuebles</p>
</div>
@endif