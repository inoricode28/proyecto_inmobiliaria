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

// INTERCEPTOR DE MORPHDOM: Proteger filas dinámicas
console.log('🔍 Verificando disponibilidad de morphdom:', typeof window.morphdom);
console.log('🔍 Verificando disponibilidad de Livewire:', typeof window.Livewire);

if (window.morphdom) {
    const originalMorphdom = window.morphdom;
    window.morphdom = function(fromNode, toNode, options = {}) {
        console.log('🔄 MORPHDOM EJECUTÁNDOSE:', fromNode, toNode);
        const originalOnBeforeElUpdated = options.onBeforeElUpdated;
        
        options.onBeforeElUpdated = function(fromEl, toEl) {
            // Si el elemento tiene atributos de protección, no permitir la actualización
            if (fromEl.hasAttribute && fromEl.hasAttribute('data-dynamic-row')) {
                console.log('🛡️ MORPHDOM INTERCEPTADO: Protegiendo fila dinámica', fromEl);
                return false; // Prevenir la actualización
            }
            
            // Si hay un callback original, ejecutarlo
            if (originalOnBeforeElUpdated) {
                return originalOnBeforeElUpdated(fromEl, toEl);
            }
            
            return true;
        };
        
        return originalMorphdom(fromNode, toNode, options);
    };
    console.log('🛡️ Interceptor de morphdom instalado para proteger filas dinámicas');
} else {
    console.log('⚠️ morphdom no está disponible aún, intentando instalar interceptor más tarde...');
    
    // Intentar instalar el interceptor cuando morphdom esté disponible
    const checkMorphdom = setInterval(() => {
        if (window.morphdom) {
            console.log('✅ morphdom ahora disponible, instalando interceptor...');
            const originalMorphdom = window.morphdom;
            window.morphdom = function(fromNode, toNode, options = {}) {
                console.log('🔄 MORPHDOM EJECUTÁNDOSE (instalado tardíamente):', fromNode, toNode);
                const originalOnBeforeElUpdated = options.onBeforeElUpdated;
                
                options.onBeforeElUpdated = function(fromEl, toEl) {
                    if (fromEl.hasAttribute && fromEl.hasAttribute('data-dynamic-row')) {
                        console.log('🛡️ MORPHDOM INTERCEPTADO (tardío): Protegiendo fila dinámica', fromEl);
                        return false;
                    }
                    
                    if (originalOnBeforeElUpdated) {
                        return originalOnBeforeElUpdated(fromEl, toEl);
                    }
                    
                    return true;
                };
                
                return originalMorphdom(fromNode, toNode, options);
            };
            console.log('🛡️ Interceptor de morphdom instalado tardíamente');
            clearInterval(checkMorphdom);
        }
    }, 100);
    
    // Limpiar el intervalo después de 10 segundos
    setTimeout(() => clearInterval(checkMorphdom), 10000);
}

// INTERCEPTOR ALTERNATIVO PARA LIVEWIRE
if (window.Livewire) {
    console.log('🔄 Instalando interceptor de Livewire...');
    
    // Interceptar eventos de Livewire que puedan causar re-renderizado
    window.Livewire.on('component.updated', (component) => {
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
    
    console.log('✅ Interceptor de Livewire instalado');
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
                console.log('🚨 ALERTA: Alguien está modificando el innerHTML del tbody!');
                console.log('📋 Nuevo valor:', value);
                console.trace('📍 Stack trace de la modificación:');
                return originalInnerHTML.set.call(this, value);
            }
        });
         
         // Interceptor para detectar cuando se remueven elementos
          const originalRemoveChild = tbody.removeChild;
          tbody.removeChild = function(child) {
              console.log('🚨 ALERTA: Se está removiendo un elemento del tbody con removeChild!');
              console.log('🗑️ Elemento removido:', child);
              console.trace('📍 Stack trace de la remoción:');
              return originalRemoveChild.call(this, child);
          };
          
          // Interceptor para el método remove() en elementos hijos
          const observer = new MutationObserver(function(mutations) {
              mutations.forEach(function(mutation) {
                  if (mutation.type === 'childList' && mutation.removedNodes.length > 0) {
                      mutation.removedNodes.forEach(function(node) {
                          if (node.nodeType === 1 && node.tagName === 'TR' && node.dataset && node.dataset.propertyId) {
                              console.log('🚨 ALERTA: Fila de propiedad removida del DOM!');
                              console.log('🗑️ Propiedad ID:', node.dataset.propertyId);
                              console.log('📋 Index:', node.dataset.index);
                              console.trace('📍 Stack trace de la remoción:');
                          }
                      });
                  }
              });
          });
          
          observer.observe(tbody, { childList: true, subtree: true });
           
           // Guardar el contenido inicial del tbody para comparar
           let lastTbodyContent = tbody.innerHTML;
           
           // Verificar cambios cada 50ms
           const contentChecker = setInterval(() => {
               const currentContent = tbody.innerHTML;
               if (currentContent !== lastTbodyContent) {
                   console.log('🔄 CAMBIO DETECTADO en el contenido del tbody!');
                   console.log('📋 Contenido anterior:', lastTbodyContent.substring(0, 200) + '...');
                   console.log('📋 Contenido actual:', currentContent.substring(0, 200) + '...');
                   console.trace('📍 Stack trace del cambio:');
                   lastTbodyContent = currentContent;
               }
           }, 50);
     }
     
     // Cargar propiedades desde la API
    loadPropertiesFromAPI();
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
                    document.getElementById('empty-message').style.display = 'none';
                    document.getElementById('totals-row').style.display = '';
                    
                    const defaultRow = document.getElementById('default-row');
                    if (defaultRow) {
                        defaultRow.style.display = 'none';
                    }
                    
                    updateCalculations();
                    
                    // Log detallado después de updateCalculations
                    const tbody = document.getElementById('properties-tbody');
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
                                console.log('📋 Contenido actual del tbody:', tbody2.innerHTML);
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
    tbody.appendChild(row);
    
    // PROTECCIÓN CONTRA MORPHDOM: Marcar la fila recién agregada
    row.setAttribute('data-dynamic-row', 'true');
    row.setAttribute('data-livewire-ignore', 'true');
    row.setAttribute('wire:ignore', 'true');
    console.log('🛡️ Fila protegida contra morphdom con atributos especiales');
    
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
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'cronograma-modal' } }));
            return;
        }
        
        if (typeof createMultipleSeparaciones !== 'function') {
            console.error('❌ createMultipleSeparaciones no está disponible');
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'cronograma-modal' } }));
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
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'cronograma-modal' } }));
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
        
        // Intentar crear separaciones (sin bloquear el modal)
        try {
            console.log('🔄 Creando separaciones múltiples...');
            await createMultipleSeparaciones(multipleData);
            console.log('✅ Separaciones creadas exitosamente');
        } catch (separacionError) {
            console.warn('⚠️ Error al crear separaciones, pero continuando con el modal:', separacionError);
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
            
            console.log('🚀 Disparando evento para abrir modal del cronograma de cuota inicial');
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'cronograma-modal' } }));
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

async function openCronogramaSFModal() {
    try {
        // Crear separaciones para las propiedades seleccionadas
        const multipleData = getMultiplePropertiesData();
        await createMultipleSeparaciones(multipleData);
        
        // Establecer los datos de múltiples propiedades para el cronograma SF
        window.multiplePropertiesData = multipleData;
        window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'cronograma-sf-modal' } }));
    } catch (error) {
        console.error('Error al crear separaciones:', error);
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
        window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'pago-separacion-modal' } }));
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
        // Transformar los datos al formato esperado por el backend
        const transformedData = {
            propiedades: multipleData.properties.map(property => ({
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

        // Marcar filas como "Con Separación" usando respuesta del backend
        const separaciones = (result && result.data && Array.isArray(result.data.separaciones))
            ? result.data.separaciones
            : (result && Array.isArray(result.separaciones) ? result.separaciones : []);
        if (separaciones && separaciones.length) {
            separaciones.forEach(sep => {
                const row = document.querySelector(`#properties-tbody tr[data-departamento-id="${sep.departamento_id}"]`);
                if (row) {
                    // Actualizar estilos y badge
                    row.classList.add('bg-green-50');
                    const proyectoCell = row.querySelector('td:nth-child(2)');
                    if (proyectoCell) {
                        // Eliminar cualquier badge existente y agregar el de separación
                        const existingBadges = proyectoCell.querySelectorAll('span.inline-flex');
                        existingBadges.forEach(el => el.remove());
                        const badge = document.createElement('span');
                        badge.className = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 ml-2';
                        badge.textContent = '✓ Con Separación';
                        proyectoCell.appendChild(badge);
                    }
                    // Guardar separacion_id en dataset para otros modales
                    row.dataset.separacionId = sep.id;
                }

                // Marcar la opción del selector como con separación y persistir el id
                const selector = document.getElementById('property-selector');
                if (selector) {
                    const option = selector.querySelector(`option[data-departamento-id="${sep.departamento_id}"]`);
                    if (option) {
                        option.classList.add('bg-green-100', 'text-green-800');
                        option.dataset.tieneSeparacion = 'true';
                        option.dataset.separacionId = sep.id;
                        if (!option.textContent.includes('✓ CON SEPARACIÓN')) {
                            option.textContent = option.textContent + ' ✓ CON SEPARACIÓN';
                        }
                    }
                }
            });
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
    
    // Buscar filas en el tbody correcto con el selector correcto
    const rows = document.querySelectorAll('#properties-tbody tr[data-index]');
    console.log('📊 Filas encontradas:', rows.length);
    console.log('🔍 Selector usado: #properties-tbody tr[data-index]');
    
    if (rows.length === 0) {
        console.log('⚠️ No se encontraron filas con data-index');
        return null;
    }
    
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
        const departamentoId = row.dataset.departamentoId || row.dataset.propertyId || null;
        
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