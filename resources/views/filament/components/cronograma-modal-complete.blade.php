{{-- Modal de Cronograma Completo --}}
<div id="cronograma-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        {{-- Background overlay --}}
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeCronogramaModal()"></div>

        {{-- Modal panel --}}
        <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                {{-- Header --}}
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Cronograma de Cuota Inicial
                    </h3>
                    <button type="button" onclick="closeCronogramaModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- Contenido del modal --}}
                <div id="cronograma-content">
                    {{-- Información del Inmueble --}}
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <h3 class="text-lg font-semibold text-blue-800 mb-3">Información del Inmueble</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-700">Proyecto:</span>
                                <div class="text-blue-600 font-semibold" id="proyecto-nombre">-</div>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Inmueble:</span>
                                <div class="text-blue-600 font-semibold" id="inmueble-numero">-</div>
                            </div>
                            <!--
                            <div>
                                <span class="font-medium text-gray-700">Precio Venta:</span>
                                <div class="text-blue-600 font-semibold" id="precio-venta">S/ 0.00</div>
                            </div>
                        -->
                            <div>
                                <span class="font-medium text-gray-700">Cuota Inicial:</span>
                                <div class="text-blue-600 font-semibold" id="cuota-inicial">S/ 0.00</div>
                            </div>
                        </div>
                    </div>

                    {{-- Formulario de Cronograma --}}
                    <div class="space-y-4 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio:</label>
                                <input type="date" id="fechaInicio" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Monto Total:</label>
                                <input type="number" id="montoTotal" class="w-full p-2 border border-gray-300 rounded-lg bg-gray-100" step="0.01" readonly>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Número de Cuotas:</label>
                                <input type="number" id="numeroCuotas" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" min="1" max="12" value="1" required>
                            </div>
                        </div>

                        <div class="flex justify-center">
                            <button type="button" id="generarCuotas" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                Generar Cuotas
                            </button>
                        </div>
                    </div>

                    {{-- Tabla de Cuotas --}}
                    <div id="cuotasSection" class="hidden">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Cronograma de Cuotas</h3>
                        <div class="overflow-x-auto max-h-96">
                            <table class="w-full border-collapse border border-gray-300">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Fecha Pago</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Monto</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Tipo</th>
                                        <th class="border border-gray-300 px-4 py-2 text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="cuotasTableBody">
                                    <!-- Las cuotas se generarán dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                    <button type="button" onclick="closeCronogramaModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancelar
                    </button>
                    <button type="button" id="guardarCronograma" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Guardar Cronograma
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Event listener para abrir el modal
    window.addEventListener('open-modal', function(event) {
        if (event.detail && event.detail.id === 'cronograma-modal') {
            openCronogramaModal();
        }
    });

    // Función para abrir el modal y cargar datos
    window.openCronogramaModal = function() {
        const modal = document.getElementById('cronograma-modal');
        if (modal) {
            modal.classList.remove('hidden');
            
            // Cargar cuotas existentes PRIMERO, luego datos de la proforma
            setTimeout(() => {
                loadExistingCuotas(); // Cargar cuotas existentes PRIMERO
                loadProformaData();   // Luego cargar datos de proforma (sin generar cuota por defecto si ya hay cuotas)
                setDefaultDate();
            }, 100); // Pequeño delay para asegurar que el DOM esté listo
        }
    };

    // Función para cerrar el modal
    window.closeCronogramaModal = function() {
        const modal = document.getElementById('cronograma-modal');
        if (modal) {
            modal.classList.add('hidden');
            resetForm();
        }
    };

    // Nueva función para cargar cuotas existentes
    function loadExistingCuotas() {
        console.log('=== CARGANDO CUOTAS EXISTENTES ===');
        
        // Intentar cargar cuotas desde múltiples fuentes
        const proformaId = getCurrentProformaId();
        let cuotasEncontradas = false;
        
        if (proformaId) {
            console.log('🔍 Cargando cuotas temporales para proforma ID:', proformaId);
            
            // Hacer petición para obtener las cuotas temporales
            fetch(`/cronograma/temporales/${proformaId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('📦 Cuotas temporales recibidas:', data);
                    
                    if (data.success && data.data && data.data.length > 0) {
                        console.log('✅ Mostrando', data.data.length, 'cuotas temporales');
                        displayExistingCuotas(data.data);
                        cuotasEncontradas = true;
                    } else {
                        console.log('ℹ️ No hay cuotas temporales');
                    }
                    
                    // SIEMPRE intentar cargar también por separación (para casos de separación definitiva)
                    loadCuotasBySeparacion(cuotasEncontradas);
                })
                .catch(error => {
                    console.error('❌ Error al cargar cuotas temporales:', error);
                    // Si falla, intentar cargar por separación
                    loadCuotasBySeparacion(false);
                });
        } else {
            console.log('⚠️ No se encontró proforma_id, intentando cargar por separación...');
            loadCuotasBySeparacion(false);
        }
    }
    
    // Función auxiliar para cargar cuotas por separación
    function loadCuotasBySeparacion(yaHayCuotasTemporales = false) {
        // Obtener el ID de la separación
        const separacionId = getCurrentSeparacionId();
        
        if (!separacionId) {
            console.log('⚠️ No se encontró ID de separación');
            
            // Si no hay cuotas temporales ni de separación, generar cuota por defecto
            if (!yaHayCuotasTemporales) {
                console.log('🔄 No hay cuotas de ningún tipo, verificando si generar cuota por defecto...');
                checkAndGenerateDefaultCuota();
            }
            return;
        }
        
        console.log('🔍 Cargando cuotas para separación ID:', separacionId);
        
        // Hacer petición para obtener las cuotas existentes
        fetch(`/cronograma/${separacionId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('📦 Cuotas de separación recibidas:', data);
                
                if (data.success && data.data && data.data.length > 0) {
                    console.log('✅ Mostrando', data.data.length, 'cuotas de separación');
                    
                    // Si ya hay cuotas temporales, agregar las de separación sin limpiar
                    if (yaHayCuotasTemporales) {
                        console.log('ℹ️ Agregando cuotas de separación a las temporales existentes');
                        appendCuotasToTable(data.data);
                    } else {
                        displayExistingCuotas(data.data);
                    }
                } else {
                    console.log('ℹ️ No hay cuotas de separación');
                    
                    // Si no hay cuotas temporales ni de separación, generar cuota por defecto
                    if (!yaHayCuotasTemporales) {
                        console.log('🔄 No hay cuotas de ningún tipo, verificando si generar cuota por defecto...');
                        checkAndGenerateDefaultCuota();
                    }
                }
            })
            .catch(error => {
                console.error('❌ Error al cargar cuotas de separación:', error);
                
                // Si no hay cuotas temporales y falla la carga de separación, generar cuota por defecto
                if (!yaHayCuotasTemporales) {
                    console.log('🔄 Error al cargar cuotas, verificando si generar cuota por defecto...');
                    checkAndGenerateDefaultCuota();
                }
            });
    }
    
    // Función auxiliar para obtener el proforma_id actual
    function getCurrentProformaId() {
        console.log('🔍 getCurrentProformaId: Iniciando búsqueda...');
        
        // Múltiples estrategias para obtener el proformaId (misma lógica que loadProformaData)
        let proformaId = null;
        
        // Estrategia 1: Buscar en selects
        const proformaSelect = document.querySelector('select[name="proforma_id"]');
        if (proformaSelect && proformaSelect.value) {
            proformaId = proformaSelect.value;
            console.log('✓ ProformaId encontrado en select proforma_id:', proformaId);
        }
        
        // Estrategia 2: Buscar en inputs ocultos
        if (!proformaId) {
            const proformaInput = document.querySelector('input[name="proforma_id"]');
            if (proformaInput && proformaInput.value) {
                proformaId = proformaInput.value;
                console.log('✓ ProformaId encontrado en input proforma_id:', proformaId);
            }
        }
        
        // Estrategia 3: Buscar en selectores específicos de Filament
        if (!proformaId) {
            const filamentSelect = document.querySelector('[data-field-wrapper="proforma_id"] select');
            if (filamentSelect && filamentSelect.value) {
                proformaId = filamentSelect.value;
                console.log('✓ ProformaId encontrado en selector Filament:', proformaId);
            }
        }
        
        // Estrategia 4: Buscar en selectores genéricos
        if (!proformaId) {
            const genericSelects = document.querySelectorAll('select');
            console.log('🔍 Buscando en', genericSelects.length, 'selectores genéricos...');
            for (let select of genericSelects) {
                if (select.name && select.name.includes('proforma') && select.value) {
                    proformaId = select.value;
                    console.log('✓ ProformaId encontrado en selector genérico:', proformaId, 'name:', select.name);
                    break;
                }
            }
        }
        
        // Estrategia 5: Buscar en atributos data-*
        if (!proformaId) {
            const dataElement = document.querySelector('[data-proforma-id]');
            if (dataElement) {
                proformaId = dataElement.getAttribute('data-proforma-id');
                console.log('✓ ProformaId encontrado en data-proforma-id:', proformaId);
            }
        }
        
        // Estrategia 6: Buscar en el contexto de Filament (más específico)
        if (!proformaId) {
            const filamentForm = document.querySelector('form[wire\\:submit]');
            if (filamentForm) {
                const proformaField = filamentForm.querySelector('select[name="proforma_id"], input[name="proforma_id"]');
                if (proformaField && proformaField.value) {
                    proformaId = proformaField.value;
                    console.log('✓ ProformaId encontrado en formulario Filament:', proformaId);
                }
            }
        }
        
        // Estrategia 7: Buscar en todos los elementos con valor
        if (!proformaId) {
            console.log('🔍 Buscando en todos los elementos del DOM...');
            const allElements = document.querySelectorAll('input, select');
            let candidatos = [];
            
            for (let element of allElements) {
                if (element.value && !isNaN(element.value) && element.value > 0) {
                    const elementInfo = {
                        tagName: element.tagName,
                        name: element.name || 'sin-nombre',
                        id: element.id || 'sin-id',
                        className: element.className || 'sin-clase',
                        value: element.value
                    };
                    candidatos.push(elementInfo);
                    
                    // Si el nombre o id contiene 'proforma', usar ese valor
                    if ((element.name && element.name.toLowerCase().includes('proforma')) || 
                        (element.id && element.id.toLowerCase().includes('proforma'))) {
                        proformaId = element.value;
                        console.log('✓ ProformaId encontrado por coincidencia de nombre/id:', proformaId);
                        break;
                    }
                }
            }
            
            console.log('🔍 Candidatos encontrados:', candidatos);
        }
        
        console.log('🔍 getCurrentProformaId resultado final:', proformaId);
        return proformaId;
    }
    
    // Función para mostrar las cuotas existentes en la tabla
    function displayExistingCuotas(cuotas) {
        console.log('🔄 Mostrando cuotas existentes en la tabla');
        
        const cuotasTableBody = document.getElementById('cuotasTableBody');
        cuotasTableBody.innerHTML = ''; // Limpiar tabla
        
        // Obtener tipos de cuota para el select
        fetch('/api/cronograma/tipos-cuota')
            .then(response => response.json())
            .then(tiposData => {
                if (tiposData.success) {
                    const tiposCuota = tiposData.data;
                    
                    cuotas.forEach((cuota, index) => {
                        console.log(`🔄 Procesando cuota ${index + 1}:`, cuota);
                        
                        const row = document.createElement('tr');
                        row.className = 'hover:bg-gray-50';
                        
                        // Crear opciones para el select de tipos
                        let tiposOptions = '';
                        tiposCuota.forEach(tipo => {
                            const selected = (tipo.id == cuota.tipo_cuota_id) ? 'selected' : '';
                            tiposOptions += `<option value="${tipo.id}" ${selected}>${tipo.nombre}</option>`;
                        });
                        
                        // Formatear fecha para input date
                        const fechaFormateada = new Date(cuota.fecha_pago).toISOString().split('T')[0];
                        console.log(`📅 Fecha formateada para cuota ${index + 1}:`, fechaFormateada);
                        
                        row.innerHTML = `
                            <td class="border border-gray-300 px-4 py-2">
                                <input type="date" value="${fechaFormateada}" class="w-full p-1 border border-gray-300 rounded focus:ring-1 focus:ring-blue-500" required>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <input type="number" value="${parseFloat(cuota.monto).toFixed(2)}" step="0.01" class="w-full p-1 border border-gray-300 rounded focus:ring-1 focus:ring-blue-500" required>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <select class="w-full p-1 border border-gray-300 rounded focus:ring-1 focus:ring-blue-500" required>
                                    ${tiposOptions}
                                </select>
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-center">
                                <button type="button" class="text-red-600 hover:text-red-800 eliminar-cuota p-1 rounded hover:bg-red-50" title="Eliminar cuota">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </td>
                        `;
                        
                        console.log(`➕ Agregando fila ${index + 1} a la tabla`);
                        cuotasTableBody.appendChild(row);
                        console.log(`✅ Fila ${index + 1} agregada exitosamente`);
                        
                        // Agregar event listener para eliminar cuota
                        row.querySelector('.eliminar-cuota').addEventListener('click', function() {
                            if (confirm('¿Está seguro de eliminar esta cuota?')) {
                                this.closest('tr').remove();
                                
                                // Si no quedan cuotas, ocultar la sección
                                if (cuotasTableBody.children.length === 0) {
                                    document.getElementById('cuotasSection').classList.add('hidden');
                                }
                            }
                        });
                    });
                    
                    // Mostrar la sección de cuotas
                    const cuotasSection = document.getElementById('cuotasSection');
                    console.log('🔍 Elemento cuotasSection encontrado:', cuotasSection);
                    
                    if (cuotasSection) {
                        cuotasSection.classList.remove('hidden');
                        console.log('👁️ Sección de cuotas mostrada');
                        
                        // Verificar que las filas se agregaron correctamente
                        console.log('📊 Filas en la tabla después de agregar:', cuotasTableBody.children.length);
                        console.log('📊 Contenido HTML de la tabla:', cuotasTableBody.innerHTML.substring(0, 200) + '...');
                    } else {
                        console.error('❌ ERROR: No se encontró el elemento cuotasSection');
                    }
                    
                    console.log('✅ Cuotas existentes mostradas correctamente');
                } else {
                    console.error('❌ Error en la respuesta de tipos de cuota:', tiposData);
                }
            })
            .catch(error => {
                console.error('❌ Error al obtener tipos de cuota:', error);
            });
    }

    // Función para cargar datos de la proforma
    function loadProformaData() {
        console.log('=== INICIANDO loadProformaData ===');
        
        // Múltiples estrategias para obtener el proformaId
        let proformaId = null;
        
        // Estrategia 1: Buscar en selects
        const proformaSelect = document.querySelector('select[name="proforma_id"]');
        if (proformaSelect && proformaSelect.value) {
            proformaId = proformaSelect.value;
            console.log('✓ ProformaId encontrado en select proforma_id:', proformaId);
        }
        
        // Estrategia 2: Buscar en inputs ocultos
        if (!proformaId) {
            const proformaInput = document.querySelector('input[name="proforma_id"]');
            if (proformaInput && proformaInput.value) {
                proformaId = proformaInput.value;
                console.log('✓ ProformaId encontrado en input proforma_id:', proformaId);
            }
        }
        
        // Estrategia 3: Buscar en selectores específicos de Filament
        if (!proformaId) {
            const filamentSelect = document.querySelector('[data-field-wrapper="proforma_id"] select');
            if (filamentSelect && filamentSelect.value) {
                proformaId = filamentSelect.value;
                console.log('✓ ProformaId encontrado en selector Filament:', proformaId);
            }
        }
        
        // Estrategia 4: Buscar en selectores genéricos
        if (!proformaId) {
            const genericSelects = document.querySelectorAll('select');
            console.log('Buscando en', genericSelects.length, 'selectores genéricos...');
            for (let select of genericSelects) {
                if (select.name && select.name.includes('proforma') && select.value) {
                    proformaId = select.value;
                    console.log('✓ ProformaId encontrado en selector genérico:', proformaId, 'name:', select.name);
                    break;
                }
            }
        }
        
        // Estrategia 5: Buscar en atributos data-*
        if (!proformaId) {
            const dataElement = document.querySelector('[data-proforma-id]');
            if (dataElement) {
                proformaId = dataElement.getAttribute('data-proforma-id');
                console.log('✓ ProformaId encontrado en data-proforma-id:', proformaId);
            }
        }
        
        // Estrategia 6: Buscar en el contexto de Filament (más específico)
        if (!proformaId) {
            const filamentForm = document.querySelector('form[wire\\:submit]');
            if (filamentForm) {
                const proformaField = filamentForm.querySelector('select[name="proforma_id"], input[name="proforma_id"]');
                if (proformaField && proformaField.value) {
                    proformaId = proformaField.value;
                    console.log('✓ ProformaId encontrado en formulario Filament:', proformaId);
                }
            }
        }
        
        // Estrategia 7: Buscar en todos los elementos con valor
        if (!proformaId) {
            console.log('Buscando en todos los elementos del DOM...');
            const allElements = document.querySelectorAll('input, select');
            let candidatos = [];
            
            for (let element of allElements) {
                if (element.value && !isNaN(element.value) && element.value > 0) {
                    const elementInfo = {
                        tagName: element.tagName,
                        name: element.name || 'sin-nombre',
                        id: element.id || 'sin-id',
                        className: element.className || 'sin-clase',
                        value: element.value
                    };
                    candidatos.push(elementInfo);
                    
                    // Si el nombre o id contiene 'proforma', usar ese valor
                    if ((element.name && element.name.toLowerCase().includes('proforma')) || 
                        (element.id && element.id.toLowerCase().includes('proforma'))) {
                        proformaId = element.value;
                        console.log('✓ ProformaId encontrado por coincidencia de nombre/id:', proformaId);
                        break;
                    }
                }
            }
            
            console.log('Candidatos encontrados:', candidatos);
        }
        
        // Estrategia 8: Buscar en URL o parámetros
        if (!proformaId) {
            const urlParams = new URLSearchParams(window.location.search);
            const urlProformaId = urlParams.get('proforma_id') || urlParams.get('id');
            if (urlProformaId) {
                proformaId = urlProformaId;
                console.log('✓ ProformaId encontrado en URL:', proformaId);
            }
        }
        
        console.log('=== RESULTADO FINAL ===');
        console.log('ProformaId final:', proformaId);
        console.log('Tipo:', typeof proformaId);
        console.log('Es válido:', proformaId && proformaId !== '' && proformaId !== '0');
        
        if (proformaId && proformaId !== '' && proformaId !== '0') {
            console.log('🚀 Realizando petición para proforma ID:', proformaId);
            
            // Intentar primero la ruta que está en el código actual
            fetch(`/api/proforma/${proformaId}/cronograma-data`)
                .then(response => {
                    console.log('📡 Respuesta HTTP status:', response.status);
                    console.log('📡 Respuesta HTTP headers:', response.headers);
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    
                    return response.json();
                })
                .then(data => {
                    console.log('📦 Datos recibidos completos:', JSON.stringify(data, null, 2));
                    if (data.success) {
                        console.log('✅ Actualizando elementos del DOM...');
                        updateModalElements(data);
                    } else {
                        console.error('❌ Error al cargar datos de la proforma:', data.message);
                        // Intentar ruta alternativa
                        tryAlternativeRoute(proformaId);
                    }
                })
                .catch(error => {
                    console.error('❌ Error en la petición principal:', error);
                    // Intentar ruta alternativa
                    tryAlternativeRoute(proformaId);
                });
        } else {
            console.warn('⚠️ No se encontró ID de proforma válido');
            console.log('Elementos disponibles en el DOM:');
            logDOMElements();
            setDefaultData();
        }
    }
    
    // Función para intentar rutas alternativas
    function tryAlternativeRoute(proformaId) {
        console.log('🔄 Intentando ruta alternativa...');
        
        // Intentar la ruta del controlador que sabemos que existe
        fetch(`/api/proformas/${proformaId}`)
            .then(response => {
                console.log('📡 Respuesta alternativa HTTP status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                return response.json();
            })
            .then(data => {
                console.log('📦 Datos de ruta alternativa:', JSON.stringify(data, null, 2));
                updateModalElements(data);
            })
            .catch(error => {
                console.error('❌ Error en ruta alternativa:', error);
                setDefaultData();
            });
    }
    
    // Función para actualizar elementos del modal con datos de la proforma
    function updateModalElements(data) {
        console.log('🔄 Actualizando elementos del modal con datos:', data);
        
        // Actualizar cliente
        const clienteElement = document.getElementById('cliente-nombre');
        if (clienteElement) {
            clienteElement.textContent = data.cliente || 'N/A';
            console.log('✅ Cliente actualizado:', data.cliente);
        } else {
            console.warn('⚠️ Elemento cliente-nombre no encontrado');
        }
        
        // Actualizar proyecto
        const proyectoElement = document.getElementById('proyecto-nombre');
        if (proyectoElement) {
            proyectoElement.textContent = data.proyecto || 'N/A';
            console.log('✅ Proyecto actualizado:', data.proyecto);
        } else {
            console.warn('⚠️ Elemento proyecto-nombre no encontrado');
        }
        
        // Actualizar inmueble
        const inmuebleElement = document.getElementById('inmueble-numero');
        if (inmuebleElement) {
            inmuebleElement.textContent = data.inmueble || 'N/A';
            console.log('✅ Inmueble actualizado:', data.inmueble);
        } else {
            console.warn('⚠️ Elemento inmueble-numero no encontrado');
        }
        
        // Actualizar precio de venta
        const precioVentaElement = document.getElementById('precio-venta');
        if (precioVentaElement) {
            precioVentaElement.textContent = data.precio_venta || 'S/ 0.00';
            console.log('✅ Precio venta actualizado:', data.precio_venta);
        } else {
            console.warn('⚠️ Elemento precio-venta no encontrado');
        }
        
        // Actualizar cuota inicial
        const cuotaInicialElement = document.getElementById('cuota-inicial');
        if (cuotaInicialElement) {
            cuotaInicialElement.textContent = data.cuota_inicial || 'S/ 0.00';
            console.log('✅ Cuota inicial actualizada:', data.cuota_inicial);
        } else {
            console.warn('⚠️ Elemento cuota-inicial no encontrado');
        }
        
        // Actualizar monto total
        const montoTotalElement = document.getElementById('montoTotal');
        if (montoTotalElement) {
            montoTotalElement.value = data.monto_cuota_inicial || 0;
            console.log('✅ Monto total actualizado:', data.monto_cuota_inicial);
        } else {
            console.warn('⚠️ Elemento montoTotal no encontrado');
        }
        
        // Generar automáticamente una cuota por defecto SOLO si NO hay cuotas existentes Y NO hay proforma_id válido
        const cuotasTableBody = document.getElementById('cuotasTableBody');
        const hasExistingCuotas = cuotasTableBody && cuotasTableBody.children.length > 0;
        const proformaId = getCurrentProformaId();
        
        // Solo generar cuota por defecto si:
        // 1. Hay monto de cuota inicial válido
        // 2. NO hay cuotas existentes en la tabla
        // 3. NO hay proforma_id válido (porque si hay proforma_id, debería cargar cuotas existentes)
        if (data.monto_cuota_inicial && data.monto_cuota_inicial > 0 && !hasExistingCuotas && !proformaId) {
            console.log('🔄 Generando cuota por defecto automáticamente (sin proforma_id ni cuotas existentes)...');
            generateDefaultCuota(data.monto_cuota_inicial);
        } else if (hasExistingCuotas) {
            console.log('ℹ️ No se genera cuota por defecto porque ya existen cuotas cargadas');
        } else if (proformaId) {
            console.log('ℹ️ No se genera cuota por defecto porque hay proforma_id válido:', proformaId);
        } else {
            console.log('ℹ️ No se genera cuota por defecto - condiciones no cumplidas');
            
            // Si no se cumplieron las condiciones pero no hay cuotas, verificar si generar por defecto
            if (!hasExistingCuotas && data.monto_cuota_inicial && data.monto_cuota_inicial > 0) {
                console.log('🔄 Verificando generación de cuota por defecto alternativa...');
                setTimeout(() => checkAndGenerateDefaultCuota(), 1000); // Esperar un poco para que se carguen las cuotas existentes
            }
        }
        
        console.log('✅ Todos los elementos procesados');
    }
    
    // Nueva función para generar una cuota por defecto automáticamente
    function generateDefaultCuota(montoTotal) {
        console.log('🔄 Generando cuota por defecto con monto:', montoTotal);
        
        // Verificar si ya existen cuotas en la tabla
        const cuotasTableBody = document.getElementById('cuotasTableBody');
        if (cuotasTableBody && cuotasTableBody.children.length > 0) {
            console.log('⚠️ Ya existen', cuotasTableBody.children.length, 'cuotas en la tabla. No se generará cuota por defecto.');
            return;
        }
        
        console.log('✅ No hay cuotas existentes, generando cuota por defecto...');
        
        // Obtener tipos de cuota desde la API
        fetch('/api/cronograma/tipos-cuota')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const tiposCuota = data.data;
                    
                    // Limpiar tabla existente (solo si no hay cuotas)
                    cuotasTableBody.innerHTML = '';
                    
                    // Buscar el ID del tipo "Cuota Inicial"
                    let cuotaInicialId = null;
                    tiposCuota.forEach(tipo => {
                        if (tipo.nombre && tipo.nombre.toLowerCase().includes('cuota inicial')) {
                            cuotaInicialId = tipo.id;
                        }
                    });
                    
                    // Crear opciones para el select de tipos
                    let tiposOptions = '';
                    tiposCuota.forEach(tipo => {
                        const selected = (cuotaInicialId && tipo.id === cuotaInicialId) ? 'selected' : '';
                        tiposOptions += `<option value="${tipo.id}" ${selected}>${tipo.nombre}</option>`;
                    });
                    
                    // Generar fecha por defecto (hoy)
                    const fechaHoy = new Date();
                    const fechaFormateada = fechaHoy.toISOString().split('T')[0];
                    
                    // Crear una sola cuota con el monto total
                    const row = document.createElement('tr');
                    row.className = 'hover:bg-gray-50';
                    
                    row.innerHTML = `
                        <td class="border border-gray-300 px-4 py-2">
                            <input type="date" value="${fechaFormateada}" class="w-full p-1 border border-gray-300 rounded focus:ring-1 focus:ring-blue-500" required>
                        </td>
                        <td class="border border-gray-300 px-4 py-2">
                            <input type="number" value="${parseFloat(montoTotal).toFixed(2)}" step="0.01" class="w-full p-1 border border-gray-300 rounded focus:ring-1 focus:ring-blue-500" required>
                        </td>
                        <td class="border border-gray-300 px-4 py-2">
                            <select class="w-full p-1 border border-gray-300 rounded focus:ring-1 focus:ring-blue-500" required>
                                ${tiposOptions}
                            </select>
                        </td>
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            <button type="button" class="text-red-600 hover:text-red-800 eliminar-cuota p-1 rounded hover:bg-red-50" title="Eliminar cuota">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </td>
                    `;
                    
                    cuotasTableBody.appendChild(row);
                    
                    // Mostrar la sección de cuotas
                    document.getElementById('cuotasSection').classList.remove('hidden');
                    
                    // Agregar event listener para eliminar cuota
                    row.querySelector('.eliminar-cuota').addEventListener('click', function() {
                        if (confirm('¿Está seguro de eliminar esta cuota?')) {
                            this.closest('tr').remove();
                            
                            // Si no quedan cuotas, ocultar la sección
                            if (cuotasTableBody.children.length === 0) {
                                document.getElementById('cuotasSection').classList.add('hidden');
                            }
                        }
                    });
                    
                    console.log('✅ Cuota por defecto generada exitosamente');
                } else {
                    console.error('❌ Error al cargar tipos de cuota para generar cuota por defecto');
                }
            })
            .catch(error => {
                console.error('❌ Error al generar cuota por defecto:', error);
            });
    }
    
    // Función para registrar elementos del DOM disponibles
    function logDOMElements() {
        console.log('=== ELEMENTOS DOM DISPONIBLES ===');
        
        const selects = document.querySelectorAll('select');
        console.log('Selects encontrados:', selects.length);
        selects.forEach((select, index) => {
            console.log(`Select ${index}:`, {
                name: select.name,
                id: select.id,
                value: select.value,
                options: Array.from(select.options).map(opt => ({text: opt.text, value: opt.value}))
            });
        });
        
        const inputs = document.querySelectorAll('input');
        console.log('Inputs encontrados:', inputs.length);
        inputs.forEach((input, index) => {
            if (input.value) {
                console.log(`Input ${index}:`, {
                    name: input.name,
                    id: input.id,
                    type: input.type,
                    value: input.value
                });
            }
        });
    }

    // Función para establecer datos por defecto
    function setDefaultData() {
        document.getElementById('proyecto-nombre').textContent = 'N/A';
        document.getElementById('inmueble-numero').textContent = 'N/A';
        document.getElementById('precio-venta').textContent = 'S/ 0.00';
        document.getElementById('cuota-inicial').textContent = 'S/ 0.00';
        // No establecer valor por defecto fijo, se cargará desde la proforma
        document.getElementById('montoTotal').value = '';
    }

    // Función para establecer fecha por defecto
    function setDefaultDate() {
        const fechaInicio = document.getElementById('fechaInicio');
        if (fechaInicio) {
            const today = new Date();
            fechaInicio.value = today.toISOString().split('T')[0];
        }
    }

    // Función para resetear el formulario
    function resetForm() {
        document.getElementById('cuotasSection').classList.add('hidden');
        document.getElementById('cuotasTableBody').innerHTML = '';
        document.getElementById('numeroCuotas').value = '3';
    }

    // Event listener para generar cuotas
    document.getElementById('generarCuotas').addEventListener('click', function() {
        const fechaInicio = document.getElementById('fechaInicio').value;
        let montoTotal = parseFloat(document.getElementById('montoTotal').value);
        const numeroCuotas = parseInt(document.getElementById('numeroCuotas').value);

        // Validar que el monto total sea válido
        if (!montoTotal || montoTotal <= 0) {
            alert('Por favor, asegúrese de que el monto total sea válido.');
            return;
        }

        if (!fechaInicio || !numeroCuotas || numeroCuotas < 1) {
            alert('Por favor, complete todos los campos correctamente.');
            return;
        }

        // Limpiar tabla existente (esto actualizará las cuotas existentes)
        const cuotasTableBody = document.getElementById('cuotasTableBody');
        cuotasTableBody.innerHTML = '';

        console.log('🔄 Regenerando cronograma - esto actualizará las cuotas existentes');

        // Obtener tipos de cuota desde la API
        fetch('/api/cronograma/tipos-cuota')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const tiposCuota = data.data;
                    
                    // Generar cuotas (esto reemplazará las existentes)
                    for (let i = 0; i < numeroCuotas; i++) {
                        const fechaCuota = new Date(fechaInicio);
                        fechaCuota.setMonth(fechaCuota.getMonth() + i);

                        const row = document.createElement('tr');
                        row.className = 'hover:bg-gray-50';
                        
                        // Crear opciones para el select de tipos
                        let tiposOptions = '';
                        let cuotaInicialId = null;
                        
                        // Buscar el ID del tipo "Cuota Inicial"
                        tiposCuota.forEach(tipo => {
                            if (tipo.nombre && tipo.nombre.toLowerCase().includes('cuota inicial')) {
                                cuotaInicialId = tipo.id;
                            }
                        });
                        
                        // TODAS las cuotas deben ser "Cuota Inicial" según requerimiento
                        tiposCuota.forEach(tipo => {
                            const selected = (cuotaInicialId && tipo.id === cuotaInicialId) ? 'selected' : '';
                            tiposOptions += `<option value="${tipo.id}" ${selected}>${tipo.nombre}</option>`;
                        });
                        
                        // Calcular monto de cuota: dividir el Monto Total entre el número de cuotas
                        const montoCuota = montoTotal / numeroCuotas;
                        
                        row.innerHTML = `
                <td class="border border-gray-300 px-4 py-2">
                    <input type="date" value="${fechaCuota.toISOString().split('T')[0]}" class="w-full p-1 border border-gray-300 rounded focus:ring-1 focus:ring-blue-500" required>
                </td>
                <td class="border border-gray-300 px-4 py-2">
                    <input type="number" value="${montoCuota.toFixed(2)}" step="0.01" class="w-full p-1 border border-gray-300 rounded focus:ring-1 focus:ring-blue-500" required>
                </td>
                <td class="border border-gray-300 px-4 py-2">
                    <select class="w-full p-1 border border-gray-300 rounded focus:ring-1 focus:ring-blue-500" required>
                        ${tiposOptions}
                    </select>
                </td>
                <td class="border border-gray-300 px-4 py-2 text-center">
                    <button type="button" class="text-red-600 hover:text-red-800 eliminar-cuota p-1 rounded hover:bg-red-50" title="Eliminar cuota">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </td>
            `;

                        cuotasTableBody.appendChild(row);
                    }

                    // Mostrar la sección de cuotas
                    document.getElementById('cuotasSection').classList.remove('hidden');

                    // Agregar event listeners para eliminar cuotas
                    document.querySelectorAll('.eliminar-cuota').forEach(btn => {
                        btn.addEventListener('click', function() {
                            if (confirm('¿Está seguro de eliminar esta cuota?')) {
                                this.closest('tr').remove();
                                
                                // Si no quedan cuotas, ocultar la sección
                                if (cuotasTableBody.children.length === 0) {
                                    document.getElementById('cuotasSection').classList.add('hidden');
                                }
                            }
                        });
                    });
                } else {
                    alert('Error al cargar los tipos de cuota');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar los tipos de cuota');
            });
    });

    // Event listener para guardar cronograma
    document.getElementById('guardarCronograma').addEventListener('click', function() {
        console.log('🔄 Iniciando proceso de guardado del cronograma...');
        
        // Proceder directamente con el guardado del cronograma
        guardarCronogramaCompleto();
    });
    
    // Función separada para guardar el cronograma
    function guardarCronogramaCompleto() {
        console.log('💾 Iniciando guardado del cronograma...');
        
        // Obtener datos de la proforma
        let proformaId = null;
        let clienteId = null;
        
        // Buscar proforma_id con diferentes selectores posibles
        const proformaSelectors = [
            'select[name="proforma_id"]',
            'select[name="data.proforma_id"]',
            'input[name="proforma_id"]',
            'input[name="data.proforma_id"]',
            'select[wire\\:model="data.proforma_id"]',
            'input[wire\\:model="data.proforma_id"]'
        ];
        
        for (const selector of proformaSelectors) {
            const element = document.querySelector(selector);
            if (element && element.value) {
                proformaId = element.value;
                console.log('✅ Proforma ID encontrado con selector:', selector, 'Valor:', proformaId);
                break;
            }
        }
        
        // Si no se encuentra, intentar obtener de la URL (para separaciones definitivas)
        if (!proformaId) {
            const urlParams = new URLSearchParams(window.location.search);
            const fromParam = urlParams.get('from');
            console.log('🔍 Parámetro "from" de la URL:', fromParam);
            
            if (fromParam && fromParam.includes('separacion_definitiva:')) {
                const parts = fromParam.split(':');
                if (parts.length >= 2) {
                    proformaId = parts[1];
                    console.log('✅ Proforma ID obtenido de la URL (separación definitiva):', proformaId);
                } else {
                    console.log('❌ Error al extraer ID del parámetro "from":', fromParam);
                }
            } else if (fromParam && fromParam === 'separacion_definitiva') {
                // Si el parámetro from es solo 'separacion_definitiva', usar el ID que ya encontró loadProformaData
                console.log('🔍 Parámetro "from" es "separacion_definitiva", buscando ID ya cargado...');
                
                // Buscar en elementos que ya tienen el ID cargado
                const elementosConId = document.querySelectorAll('[id*="proforma"], [name*="proforma"]');
                for (const elemento of elementosConId) {
                    if (elemento.value && elemento.value !== '' && !isNaN(elemento.value)) {
                        proformaId = elemento.value;
                        console.log('✅ Proforma ID encontrado en elemento ya cargado:', elemento.tagName, elemento.id || elemento.name, 'Valor:', proformaId);
                        break;
                    }
                }
                
                // Si aún no se encuentra, buscar en el DOM global
                if (!proformaId) {
                    const todosLosElementos = document.querySelectorAll('input, select, textarea');
                    for (const elemento of todosLosElementos) {
                        if (elemento.value && !isNaN(elemento.value) && elemento.value.length < 10) {
                            // Verificar si este valor podría ser un ID de proforma válido
                            const valor = parseInt(elemento.value);
                            if (valor > 0 && valor < 100000) {
                                proformaId = elemento.value;
                                console.log('✅ Posible Proforma ID encontrado en:', elemento.tagName, elemento.id || elemento.name || 'sin-nombre', 'Valor:', proformaId);
                                break;
                            }
                        }
                    }
                }
            } else {
                console.log('❌ Parámetro "from" no contiene "separacion_definitiva:"');
            }
        }
        
        // Buscar cliente_id con diferentes selectores posibles
        const clienteSelectors = [
            'select[name="cliente_id"]',
            'select[name="data.cliente_id"]',
            'input[name="cliente_id"]',
            'input[name="data.cliente_id"]',
            'select[wire\\:model="data.cliente_id"]',
            'input[wire\\:model="data.cliente_id"]'
        ];
        
        for (const selector of clienteSelectors) {
            const element = document.querySelector(selector);
            if (element && element.value) {
                clienteId = element.value;
                console.log('✅ Cliente ID encontrado con selector:', selector, 'Valor:', clienteId);
                break;
            }
        }
        
        if (!proformaId) {
            console.error('❌ No se pudo encontrar el ID de la proforma. Selectores probados:', proformaSelectors);
            console.log('🔍 Elementos del formulario disponibles:');
            const allInputs = document.querySelectorAll('input, select');
            allInputs.forEach(input => {
                if (input.name && (input.name.includes('proforma') || input.name.includes('cliente'))) {
                    console.log('- Elemento encontrado:', input.tagName, 'name="' + input.name + '"', 'value="' + input.value + '"');
                }
            });
            alert('Error: No se pudo obtener el ID de la proforma. Revisa la consola para más detalles.');
            return;
        }
        
        console.log('✅ Datos obtenidos - Proforma ID:', proformaId, 'Cliente ID:', clienteId);
        
        const cuotas = [];
        const rows = document.querySelectorAll('#cuotasTableBody tr');
        
        rows.forEach(row => {
            const fecha = row.querySelector('input[type="date"]').value;
            const monto = parseFloat(row.querySelector('input[type="number"]').value);
            const tipoSelect = row.querySelector('select');
            const tipoCuotaId = tipoSelect.value;
            const tipoCuotaNombre = tipoSelect.options[tipoSelect.selectedIndex].text;
            
            if (fecha && monto && tipoCuotaId) {
                cuotas.push({
                    fecha_pago: fecha,
                    monto: monto,
                    tipo_cuota_id: tipoCuotaId,
                    tipo_cuota_nombre: tipoCuotaNombre
                });
            }
        });
        
        if (cuotas.length === 0) {
            alert('No hay cuotas para guardar');
            return;
        }
        
        // Preparar datos para enviar al servidor
        const cronogramaData = {
            proforma_id: proformaId,
            cliente_id: clienteId,
            cuotas: cuotas,
            _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        };
        
        console.log('📤 Enviando cronograma:', cronogramaData);
        
        // Enviar datos al servidor usando fetch
        fetch('/cronograma/guardar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': cronogramaData._token,
                'Accept': 'application/json'
            },
            body: JSON.stringify(cronogramaData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('✅ Cronograma guardado:', data);
            if (data.success) {
                alert('Cronograma guardado exitosamente' + (data.data.separacion_creada ? ' (Separación creada automáticamente)' : ''));
                // Cerrar el modal
                document.getElementById('cronograma-modal').classList.add('hidden');
                window.location.reload();
            } else {
                alert('Error al guardar: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('❌ Error al guardar cronograma:', error);
            alert('Error al guardar el cronograma: ' + error.message);
        });
    }
    
    // Función para obtener el ID de la separación actual (definida globalmente)
    function getCurrentSeparacionId() {
        // Estrategia especial para separación definitiva en proceso de creación
        const urlParams = new URLSearchParams(window.location.search);
        const fromSeparacionDefinitiva = urlParams.get('from') === 'separacion_definitiva';
        
        if (fromSeparacionDefinitiva) {
            console.log('🔄 Detectado flujo de separación definitiva en creación');
            
            // Buscar en el formulario de Filament el ID de la separación recién creada
            const separacionIdInput = document.querySelector('input[name="separacion_id"]');
            if (separacionIdInput && separacionIdInput.value) {
                console.log('🔍 Separación ID encontrado en input del formulario:', separacionIdInput.value);
                return separacionIdInput.value;
            }
            
            // Buscar en datos de Livewire para separación recién creada
            if (typeof Livewire !== 'undefined' && Livewire.components) {
                try {
                    for (let component of Object.values(Livewire.components)) {
                        if (component && 
                            typeof component === 'object' && 
                            component.data && 
                            typeof component.data === 'object') {
                            
                            // Buscar separacion_id en los datos del componente
                            if (component.data.separacion_id) {
                                console.log('🔍 Separación ID encontrado en Livewire data:', component.data.separacion_id);
                                return component.data.separacion_id;
                            }
                            
                            // Buscar en record si existe
                            if (component.data.record && 
                                typeof component.data.record === 'object' && 
                                component.data.record.id) {
                                console.log('🔍 Separación ID encontrado en Livewire record:', component.data.record.id);
                                return component.data.record.id;
                            }
                            
                            // También buscar en data directamente
                            if (component.data.id) {
                                console.log('🔍 Separación ID encontrado en data de Livewire:', component.data.id);
                                return component.data.id;
                            }
                        }
                    }
                } catch (error) {
                    console.warn('⚠️ Error al buscar en componentes Livewire:', error);
                }
            }
            
            // Buscar en elementos del DOM que puedan contener el ID recién creado
            const createdRecordElement = document.querySelector('[data-record-id]');
            if (createdRecordElement) {
                const recordId = createdRecordElement.getAttribute('data-record-id');
                console.log('🔍 Separación ID encontrado en elemento creado:', recordId);
                return recordId;
            }
            
            // Si estamos en proceso de creación, devolver null para indicar que aún no hay ID
            console.log('⏳ Separación en proceso de creación, ID aún no disponible');
            return null;
        }
        
        // Estrategia 1: Buscar en el URL (para páginas de detalle)
        let separacionId = urlParams.get('separacion_id');
        
        if (separacionId) {
            console.log('🔍 Separación ID encontrado en URL:', separacionId);
            return separacionId;
        }
        
        // Estrategia 2: Buscar en la URL actual (para páginas de detalle con ID en la ruta)
        const pathParts = window.location.pathname.split('/');
        const separacionIndex = pathParts.indexOf('separacions');
        if (separacionIndex !== -1 && pathParts[separacionIndex + 1]) {
            separacionId = pathParts[separacionIndex + 1];
            console.log('🔍 Separación ID encontrado en ruta:', separacionId);
            return separacionId;
        }
        
        // Estrategia 3: Buscar en elementos del DOM
        const separacionElement = document.querySelector('[data-separacion-id]');
        if (separacionElement) {
            separacionId = separacionElement.getAttribute('data-separacion-id');
            console.log('🔍 Separación ID encontrado en DOM:', separacionId);
            return separacionId;
        }
        
        // Estrategia 4: Buscar en variables globales de JavaScript
        if (typeof window.separacionId !== 'undefined') {
            console.log('🔍 Separación ID encontrado en variable global:', window.separacionId);
            return window.separacionId;
        }
        
        // Estrategia 5: Buscar en el contexto de Filament/Livewire
        if (typeof Livewire !== 'undefined' && Livewire.components) {
            try {
                for (let component of Object.values(Livewire.components)) {
                    // Validación más robusta para evitar errores de propiedades undefined
                    if (component && 
                        typeof component === 'object' && 
                        component.data && 
                        typeof component.data === 'object' && 
                        component.data.record && 
                        typeof component.data.record === 'object' && 
                        component.data.record.id) {
                        console.log('🔍 Separación ID encontrado en Livewire:', component.data.record.id);
                        return component.data.record.id;
                    }
                }
            } catch (error) {
                console.warn('⚠️ Error al buscar en componentes Livewire:', error);
            }
        }
        
        // Estrategia 6: Buscar en meta tags
        const metaSeparacionId = document.querySelector('meta[name="separacion-id"]');
        if (metaSeparacionId) {
            separacionId = metaSeparacionId.getAttribute('content');
            console.log('🔍 Separación ID encontrado en meta tag:', separacionId);
            return separacionId;
        }
        
        // Estrategia 7: Buscar en el título o breadcrumbs
        const titleElement = document.querySelector('h1, .fi-header-heading');
        if (titleElement && titleElement.textContent) {
            const match = titleElement.textContent.match(/separaci[óo]n\s*#?(\d+)/i);
            if (match) {
                separacionId = match[1];
                console.log('🔍 Separación ID encontrado en título:', separacionId);
                return separacionId;
            }
        }
        
        console.log('❌ No se pudo encontrar el ID de la separación');
        return null;
    }

    // Función auxiliar para agregar cuotas a la tabla sin limpiarla
    function appendCuotasToTable(cuotas) {
        console.log('📝 Agregando', cuotas.length, 'cuotas adicionales a la tabla');
        
        const cuotasTableBody = document.getElementById('cuotasTableBody');
        if (!cuotasTableBody) {
            console.error('❌ ERROR: No se encontró el elemento cuotasTableBody');
            return;
        }
        
        // Obtener tipos de cuota
        fetch('/api/tipos-cuota')
            .then(response => response.json())
            .then(tiposData => {
                if (tiposData.success && tiposData.data) {
                    const tiposCuota = tiposData.data;
                    console.log('✅ Tipos de cuota obtenidos para agregar:', tiposCuota.length);
                    
                    cuotas.forEach((cuota, index) => {
                        console.log(`Agregando cuota adicional ${index + 1}:`, cuota);
                        
                        const row = document.createElement('tr');
                        const currentRowIndex = cuotasTableBody.children.length;
                        
                        // Fecha de pago
                        const fechaCell = document.createElement('td');
                        fechaCell.innerHTML = `<input type="date" class="form-control" value="${cuota.fecha_pago || ''}" name="cuotas[${currentRowIndex}][fecha_pago]">`;
                        row.appendChild(fechaCell);
                        
                        // Monto
                        const montoCell = document.createElement('td');
                        montoCell.innerHTML = `<input type="number" class="form-control" value="${cuota.monto || 0}" step="0.01" name="cuotas[${currentRowIndex}][monto]">`;
                        row.appendChild(montoCell);
                        
                        // Tipo de cuota
                        const tipoCell = document.createElement('td');
                        let tipoOptions = tiposCuota.map(tipo => 
                            `<option value="${tipo.id}" ${tipo.id == cuota.tipo_cuota_id ? 'selected' : ''}>${tipo.nombre}</option>`
                        ).join('');
                        tipoCell.innerHTML = `<select class="form-control" name="cuotas[${currentRowIndex}][tipo_cuota_id]">${tipoOptions}</select>`;
                        row.appendChild(tipoCell);
                        
                        // Acciones
                        const accionesCell = document.createElement('td');
                        accionesCell.innerHTML = `<button type="button" class="btn btn-danger btn-sm" onclick="eliminarCuota(this)"><i class="fas fa-trash"></i></button>`;
                        row.appendChild(accionesCell);
                        
                        cuotasTableBody.appendChild(row);
                        console.log(`Cuota adicional ${index + 1} agregada a la tabla`);
                    });
                    
                    // Mostrar la sección de cuotas
                    const cuotasSection = document.getElementById('cuotasSection');
                    if (cuotasSection) {
                        cuotasSection.style.display = 'block';
                        console.log('✅ Sección de cuotas mostrada');
                    }
                    
                    console.log('✅ Cuotas adicionales agregadas correctamente');
                } else {
                    console.error('❌ Error en la respuesta de tipos de cuota:', tiposData);
                }
            })
            .catch(error => {
                console.error('❌ Error al obtener tipos de cuota para agregar:', error);
            });
    }

    // Función para verificar y generar cuota por defecto cuando no hay cuotas
    function checkAndGenerateDefaultCuota() {
        console.log('🔍 Verificando si generar cuota por defecto...');
        
        const cuotasTableBody = document.getElementById('cuotasTableBody');
        const hasExistingCuotas = cuotasTableBody && cuotasTableBody.children.length > 0;
        
        if (hasExistingCuotas) {
            console.log('ℹ️ Ya hay cuotas en la tabla, no se genera cuota por defecto');
            return;
        }
        
        // Obtener el monto total
        const montoTotalElement = document.getElementById('montoTotal');
        const montoTotal = montoTotalElement ? parseFloat(montoTotalElement.value) : 0;
        
        if (montoTotal > 0) {
            console.log('🔄 Generando cuota por defecto con monto:', montoTotal);
            generateDefaultCuota(montoTotal);
        } else {
            console.log('⚠️ No se puede generar cuota por defecto: monto total no válido');
        }
    }
});
</script>

<style>
/* Estilos adicionales para el modal */
.max-h-96 {
    max-height: 24rem;
}

.sticky {
    position: sticky;
}

.top-0 {
    top: 0;
}

#cronograma-modal {
    backdrop-filter: blur(4px);
}
</style>