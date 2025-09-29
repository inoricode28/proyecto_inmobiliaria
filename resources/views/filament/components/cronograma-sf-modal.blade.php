{{-- Modal de Cronograma de Saldo a Financiar --}}
<div>
<div id="cronograma-sf-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        {{-- Background overlay --}}
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeCronogramaSFModal()"></div>

        {{-- Modal panel --}}
        <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                {{-- Header --}}
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Cronograma de Saldo a Financiar
                    </h3>
                    <button type="button" onclick="closeCronogramaSFModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- Contenido del modal --}}
                <div id="cronograma-sf-content">
                    {{-- Información de la proforma --}}
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <h4 class="text-md font-semibold text-blue-800 mb-2">Información de la Proforma</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-700">Proyecto:</span>
                                <span id="sf-proyecto-nombre" class="text-gray-900">Cargando...</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Inmueble:</span>
                                <span id="sf-inmueble-numero" class="text-gray-900">Cargando...</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Saldo a Financiar:</span>
                                <span id="sf-saldo-financiar" class="text-gray-900 font-semibold">S/ 0.00</span>
                            </div>
                        </div>
                    </div>

                    {{-- Cuotas existentes --}}
                    <div id="sf-cuotasExistentesSection" class="hidden mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3">📋 Cuotas de Saldo a Financiar Existentes</h4>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <p class="text-sm text-blue-700">
                                <i class="fas fa-info-circle mr-2"></i>
                                Se encontraron cuotas de saldo a financiar existentes. Puede modificarlas o agregar nuevas cuotas.
                            </p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse border border-gray-300">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="border border-gray-300 px-4 py-2 text-left">N°</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Fecha Pago</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Monto</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Motivo</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Estado</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Entidad</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Tipo</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Comprobante</th>
                                    </tr>
                                </thead>
                                <tbody id="sf-cuotasExistentesTableBody">
                                    {{-- Las cuotas existentes se cargarán dinámicamente --}}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Formulario de Cronograma SF --}}
                    <div class="space-y-4 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio:</label>
                                <input type="date" id="sf-fechaInicio" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Monto:</label>
                                <input type="number" id="sf-montoTotal" class="w-full p-2 border border-gray-300 rounded-lg bg-gray-100" step="0.01" readonly>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">N° Cuotas:</label>
                                <input type="number" id="sf-numeroCuotas" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" min="1" max="60" value="1" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Motivo:</label>
                                <input type="text" id="sf-motivo" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Saldo a financiar" value="Saldo a financiar">
                            </div>
                        </div>

                        {{-- Campos específicos del cronograma SF --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo Comprobante (Opcional):</label>
                                <select id="sf-tipoComprobante" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Seleccionar...</option>
                                    <option value="BOLETA">BOLETA</option>
                                    <option value="FACTURA">FACTURA</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Entidad Financiera:</label>
                                <select id="sf-entidadFinanciera" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">Seleccionar banco...</option>
                                    {{-- Los bancos se cargarán dinámicamente --}}
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Financiamiento:</label>
                                <select id="sf-tipoFinanciamiento" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">Seleccionar tipo...</option>
                                    {{-- Los tipos se cargarán dinámicamente --}}
                                </select>
                            </div>
                        </div>

                        {{-- Checkboxes para tipos de bono --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="sf-bonoMiVivienda" class="mr-2">
                                <label for="sf-bonoMiVivienda" class="text-sm font-medium text-gray-700">Bono Mi Vivienda</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="sf-bonoVerde" class="mr-2">
                                <label for="sf-bonoVerde" class="text-sm font-medium text-gray-700">Bono Verde</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="sf-bonoIntegrador" class="mr-2">
                                <label for="sf-bonoIntegrador" class="text-sm font-medium text-gray-700">Bono Integrador</label>
                            </div>
                        </div>

                        <div class="flex justify-center">
                            <button type="button" id="sf-generarCuotas" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                Generar Cuotas
                            </button>
                        </div>
                    </div>

                    {{-- Tabla de Cuotas --}}
                    <div id="sf-cuotasSection" class="hidden">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Cronograma de Cuotas - Saldo a Financiar</h3>
                        <div class="overflow-x-auto max-h-96">
                            <table class="w-full border-collapse border border-gray-300">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="border border-gray-300 px-4 py-2 text-left">#Cuotas</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Fecha Pago</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Monto</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Motivo</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="sf-cuotasTableBody">
                                    {{-- Las cuotas se generarán dinámicamente --}}
                                </tbody>
                            </table>
                        </div>

                        {{-- Totales --}}
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Saldo Financiar:</label>
                                    <input type="number" id="sf-totalSaldoFinanciar" class="w-full p-2 border border-gray-300 rounded-lg bg-gray-100" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Diferencia:</label>
                                    <input type="number" id="sf-diferencia" class="w-full p-2 border border-gray-300 rounded-lg bg-gray-100" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                    <button type="button" onclick="closeCronogramaSFModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancelar
                    </button>
                    <button type="button" id="sf-aceptarSaldoFinanciar" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Aceptar Saldo Financiar
                    </button>
                    <button type="button" onclick="closeCronogramaSFModal()" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Event listener para abrir el modal de cronograma SF
    window.addEventListener('open-modal', function(event) {
        if (event.detail && event.detail.id === 'cronograma-sf-modal') {
            openCronogramaSFModal();
        }
    });

    // Función para abrir el modal y cargar datos
    window.openCronogramaSFModal = function() {
        const modal = document.getElementById('cronograma-sf-modal');
        if (modal) {
            modal.classList.remove('hidden');
            
            // Cargar datos de la proforma
        setTimeout(() => {
            loadProformaSFData();
            loadExistingCuotasSF(); // Cargar cuotas existentes
            setDefaultSFDate();
            loadBancos();
            loadTiposFinanciamiento();
            loadTiposComprobante();
        }, 100);
        }
    };

    // Función para cerrar el modal
    window.closeCronogramaSFModal = function() {
        const modal = document.getElementById('cronograma-sf-modal');
        if (modal) {
            modal.classList.add('hidden');
            resetSFForm();
        }
    };

    // Función para obtener el ID de la separación actual (copiada del modal principal)
    function getCurrentSeparacionId() {
        // Estrategia especial para separación definitiva en proceso de creación
        const urlParams = new URLSearchParams(window.location.search);
        const fromSeparacionDefinitiva = urlParams.get('from') === 'separacion_definitiva';
        
        if (fromSeparacionDefinitiva) {
            // console.log('🔄 SF: Detectado flujo de separación definitiva en creación');
            
            // Buscar en el formulario de Filament el ID de la separación recién creada
            const separacionIdInput = document.querySelector('input[name="separacion_id"]');
            if (separacionIdInput && separacionIdInput.value) {
                // console.log('🔍 SF: Separación ID encontrado en input del formulario:', separacionIdInput.value);
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
                                // console.log('🔍 SF: Separación ID encontrado en Livewire data:', component.data.separacion_id);
                                return component.data.separacion_id;
                            }
                            
                            // Buscar en record si existe
                            if (component.data.record && 
                                typeof component.data.record === 'object' && 
                                component.data.record.id) {
                                // console.log('🔍 SF: Separación ID encontrado en Livewire record:', component.data.record.id);
                                return component.data.record.id;
                            }
                            
                            // También buscar en data directamente
                            if (component.data.id) {
                                // console.log('🔍 SF: Separación ID encontrado en data de Livewire:', component.data.id);
                                return component.data.id;
                            }
                        }
                    }
                } catch (error) {
                    console.warn('⚠️ SF: Error al buscar en componentes Livewire:', error);
                }
            }
            
            // Buscar en elementos del DOM que puedan contener el ID recién creado
            const createdRecordElement = document.querySelector('[data-record-id]');
            if (createdRecordElement) {
                const recordId = createdRecordElement.getAttribute('data-record-id');
                // console.log('🔍 SF: Separación ID encontrado en elemento creado:', recordId);
                return recordId;
            }
            
            // Si estamos en proceso de creación, devolver null para indicar que aún no hay ID
            // console.log('⏳ SF: Separación en proceso de creación, ID aún no disponible');
            return null;
        }
        
        // Estrategia 1: Buscar en el URL (para páginas de detalle)
        let separacionId = urlParams.get('separacion_id');
        
        if (separacionId) {
            // console.log('🔍 SF: Separación ID encontrado en URL:', separacionId);
            return separacionId;
        }
        
        // Estrategia 2: Buscar en la URL actual (para páginas de detalle con ID en la ruta)
        const pathParts = window.location.pathname.split('/');
        const separacionIndex = pathParts.indexOf('separacions');
        if (separacionIndex !== -1 && pathParts[separacionIndex + 1]) {
            separacionId = pathParts[separacionIndex + 1];
            // console.log('🔍 SF: Separación ID encontrado en ruta:', separacionId);
            return separacionId;
        }
        
        // Estrategia 3: Buscar en elementos del DOM
        const separacionElement = document.querySelector('[data-separacion-id]');
        if (separacionElement) {
            separacionId = separacionElement.getAttribute('data-separacion-id');
            // console.log('🔍 SF: Separación ID encontrado en DOM:', separacionId);
            return separacionId;
        }
        
        // Estrategia 4: Buscar en variables globales de JavaScript
        if (typeof window.separacionId !== 'undefined') {
            // console.log('🔍 SF: Separación ID encontrado en variable global:', window.separacionId);
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
                        // console.log('🔍 SF: Separación ID encontrado en Livewire:', component.data.record.id);
                        return component.data.record.id;
                    }
                }
            } catch (error) {
                // console.warn('⚠️ SF: Error al buscar en componentes Livewire:', error);
            }
        }
        
        // Estrategia 6: Buscar en meta tags
        const metaSeparacionId = document.querySelector('meta[name="separacion-id"]');
        if (metaSeparacionId) {
            separacionId = metaSeparacionId.getAttribute('content');
            // console.log('🔍 SF: Separación ID encontrado en meta tag:', separacionId);
            return separacionId;
        }
        
        // Estrategia 7: Buscar en el título o breadcrumbs
        const titleElement = document.querySelector('h1, .fi-header-heading');
        if (titleElement && titleElement.textContent) {
            const match = titleElement.textContent.match(/separaci[óo]n\s*#?(\d+)/i);
            if (match) {
                separacionId = match[1];
                // console.log('🔍 SF: Separación ID encontrado en título:', separacionId);
                return separacionId;
            }
        }
        
        // console.log('❌ SF: No se pudo encontrar separacion_id');
        return null;
    }

    // Función para cargar datos de la proforma para SF
    function loadProformaSFData() {
        // console.log('=== CARGANDO DATOS PROFORMA PARA SF ===');
        
        // Múltiples estrategias para obtener el proformaId (igual que el modal principal)
        let proformaId = null;
        
        // Estrategia 1: Buscar en selects
        const proformaSelect = document.querySelector('select[name="proforma_id"]');
        if (proformaSelect && proformaSelect.value) {
            proformaId = proformaSelect.value;
            // console.log('✓ ProformaId SF encontrado en select proforma_id:', proformaId);
        }
        
        // Estrategia 2: Buscar en inputs ocultos
        if (!proformaId) {
            const proformaInput = document.querySelector('input[name="proforma_id"]');
            if (proformaInput && proformaInput.value) {
                proformaId = proformaInput.value;
                // console.log('✓ ProformaId SF encontrado en input proforma_id:', proformaId);
            }
        }
        
        // Estrategia 3: Buscar en selectores específicos de Filament
        if (!proformaId) {
            const filamentSelect = document.querySelector('[data-field-wrapper="proforma_id"] select');
            if (filamentSelect && filamentSelect.value) {
                proformaId = filamentSelect.value;
                // console.log('✓ ProformaId SF encontrado en selector Filament:', proformaId);
            }
        }
        
        // Estrategia 4: Buscar en selectores genéricos
        if (!proformaId) {
            const genericSelects = document.querySelectorAll('select');
            // console.log('SF: Buscando en', genericSelects.length, 'selectores genéricos...');
            for (let select of genericSelects) {
                if (select.name && select.name.includes('proforma') && select.value) {
                    proformaId = select.value;
                    // console.log('✓ ProformaId SF encontrado en selector genérico:', proformaId, 'name:', select.name);
                    break;
                }
            }
        }
        
        // Estrategia 5: Buscar en atributos data-*
        if (!proformaId) {
            const dataElement = document.querySelector('[data-proforma-id]');
            if (dataElement) {
                proformaId = dataElement.getAttribute('data-proforma-id');
                // console.log('✓ ProformaId SF encontrado en data-proforma-id:', proformaId);
            }
        }
        
        // Estrategia 6: Buscar en el contexto de Filament (más específico)
        if (!proformaId) {
            const filamentForm = document.querySelector('form[wire\\:submit]');
            if (filamentForm) {
                const proformaField = filamentForm.querySelector('select[name="proforma_id"], input[name="proforma_id"]');
                if (proformaField && proformaField.value) {
                    proformaId = proformaField.value;
                    // console.log('✓ ProformaId SF encontrado en formulario Filament:', proformaId);
                }
            }
        }
        
        // Estrategia 7: Buscar en todos los elementos con valor
        if (!proformaId) {
            // console.log('SF: Buscando en todos los elementos del DOM...');
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
                        // console.log('✓ ProformaId SF encontrado por coincidencia de nombre/id:', proformaId);
                        break;
                    }
                }
            }
            
            // console.log('SF: Candidatos encontrados:', candidatos);
        }
        
        // Estrategia 8: Buscar en URL o parámetros
        if (!proformaId) {
            const urlParams = new URLSearchParams(window.location.search);
            const urlProformaId = urlParams.get('proforma_id') || urlParams.get('id');
            if (urlProformaId) {
                proformaId = urlProformaId;
                // console.log('✓ ProformaId SF encontrado en URL:', proformaId);
            }
        }
        
        // console.log('=== RESULTADO FINAL SF ===');
        // console.log('ProformaId SF final:', proformaId);
        // console.log('Tipo:', typeof proformaId);
        // console.log('Es válido:', proformaId && proformaId !== '' && proformaId !== '0');
        
        if (proformaId) {
            // console.log('🚀 Cargando datos SF para proforma ID:', proformaId);
            
            fetch(`/api/proforma/${proformaId}/cronograma-data`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // console.log('✅ Datos SF cargados:', data);
                        // console.log('📊 Saldo a financiar recibido:', data.saldo_financiar);
                        // console.log('📊 Precio venta recibido:', data.precio_venta);
                        
                        // Usar exactamente los mismos campos del controlador
                        document.getElementById('sf-proyecto-nombre').textContent = data.proyecto || 'N/A';
                        document.getElementById('sf-inmueble-numero').textContent = data.inmueble || 'N/A';
                        
                        // Usar saldo_financiar directamente del controlador
                        let saldoFinanciar = 0;
                        
                        if (data.saldo_financiar) {
                            saldoFinanciar = parseFloat(data.saldo_financiar) || 0;
                        } else if (data.precio_venta) {
                            // Fallback: usar precio_venta si saldo_financiar no está disponible
                            if (typeof data.precio_venta === 'string') {
                                saldoFinanciar = parseFloat(data.precio_venta.replace(/,/g, '')) || 0;
                            } else {
                                saldoFinanciar = parseFloat(data.precio_venta) || 0;
                            }
                        }
                        
                        // console.log('💰 Saldo a financiar final:', saldoFinanciar);
                        
                        document.getElementById('sf-saldo-financiar').textContent = 'S/ ' + saldoFinanciar.toLocaleString('es-PE', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        document.getElementById('sf-montoTotal').value = saldoFinanciar;
                        
                        // Guardar IDs para uso posterior
                        window.currentProformaId = proformaId;
                        
                        // Cargar cuotas existentes primero
                        loadExistingCuotasSF();
                    } else {
                        // console.error('❌ Error en respuesta SF:', data.message || 'Sin mensaje de error');
                        setDefaultSFData();
                    }
                })
                .catch(error => {
                    // console.error('❌ Error al cargar datos SF:', error);
                    setDefaultSFData();
                });
        } else {
            // console.warn('⚠️ No se pudo obtener proforma_id para SF');
            setDefaultSFData();
        }
    }

    // Función para establecer datos por defecto SF
    function setDefaultSFData() {
        document.getElementById('sf-proyecto-nombre').textContent = 'N/A';
        document.getElementById('sf-inmueble-numero').textContent = 'N/A';
        document.getElementById('sf-saldo-financiar').textContent = 'S/ 0.00';
        document.getElementById('sf-montoTotal').value = 0;
    }

    // Función para establecer fecha por defecto
    function setDefaultSFDate() {
        const today = new Date();
        const formattedDate = today.toISOString().split('T')[0];
        document.getElementById('sf-fechaInicio').value = formattedDate;
    }

    // Función para cargar bancos
    function loadBancos() {
        fetch('/api/bancos')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const select = document.getElementById('sf-entidadFinanciera');
                    select.innerHTML = '<option value="">Seleccionar banco...</option>';
                    
                    data.data.forEach(banco => {
                        const option = document.createElement('option');
                        option.value = banco.id;
                        option.textContent = banco.nombre;
                        select.appendChild(option);
                    });
                } else {
                    // console.error('Error al cargar bancos:', data.message);
                }
            })
            .catch(error => {
                // console.error('Error al cargar bancos:', error);
            });
    }

    // Función para cargar tipos de financiamiento
    function loadTiposFinanciamiento() {
        fetch('/api/tipos-financiamiento')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const select = document.getElementById('sf-tipoFinanciamiento');
                    select.innerHTML = '<option value="">Seleccionar tipo...</option>';
                    
                    data.data.forEach(tipo => {
                        const option = document.createElement('option');
                        option.value = tipo.id;
                        option.textContent = tipo.nombre;
                        select.appendChild(option);
                    });
                } else {
                    // console.error('Error al cargar tipos de financiamiento:', data.message);
                }
            })
            .catch(error => {
                // console.error('Error al cargar tipos de financiamiento:', error);
            });
    }

    // Función para cargar tipos de comprobante
    function loadTiposComprobante() {
        fetch('/api/tipos-comprobante')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const select = document.getElementById('sf-tipoComprobante');
                    select.innerHTML = '<option value="">Seleccionar...</option>';
                    
                    data.data.forEach(tipo => {
                        const option = document.createElement('option');
                        option.value = tipo.id;
                        option.textContent = tipo.nombre;
                        select.appendChild(option);
                    });
                } else {
                    // console.error('Error al cargar tipos de comprobante:', data.message);
                }
            })
            .catch(error => {
                // console.error('Error al cargar tipos de comprobante:', error);
            });
    }

    // Función para cargar cuotas existentes de saldo a financiar
    function loadExistingCuotasSF() {
        // console.log('=== CARGANDO CUOTAS EXISTENTES SF ===');
        
        const separacionId = getCurrentSeparacionId();
        const proformaId = window.currentProformaId;
        
        if (!proformaId) {
            // console.log('⚠️ No hay proforma_id disponible para cargar cuotas SF');
            return;
        }

        // Primero intentar cargar cuotas definitivas (con separacion_id)
        if (separacionId) {
            // console.log('🔍 Cargando cuotas SF definitivas para separacion_id:', separacionId);
            fetch(`/cronograma-sf/${separacionId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        // console.log('✅ Cuotas SF definitivas encontradas:', data.data.length);
                        displayExistingCuotasSF(data.data, 'Definitivas');
                    } else {
                        // console.log('ℹ️ No hay cuotas SF definitivas, buscando temporales...');
                        loadTemporaryCuotasSF(proformaId);
                    }
                })
                .catch(error => {
                    // console.error('❌ Error al cargar cuotas SF definitivas:', error);
                    loadTemporaryCuotasSF(proformaId);
                });
        } else {
            // Si no hay separacion_id, buscar cuotas definitivas por proforma_id primero
            // console.log('🔍 No hay separacion_id, buscando cuotas SF definitivas por proforma_id:', proformaId);
            fetch(`/cronograma-sf/definitivas/${proformaId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        // console.log('✅ Cuotas SF definitivas encontradas por proforma_id:', data.data.length);
                        displayExistingCuotasSF(data.data, 'Definitivas');
                    } else {
                        // console.log('ℹ️ No hay cuotas SF definitivas, buscando temporales...');
                        loadTemporaryCuotasSF(proformaId);
                    }
                })
                .catch(error => {
                    // console.error('❌ Error al cargar cuotas SF definitivas por proforma:', error);
                    loadTemporaryCuotasSF(proformaId);
                });
        }
    }

    // Función para cargar cuotas temporales SF
    function loadTemporaryCuotasSF(proformaId) {
        // console.log('🔍 Cargando cuotas SF temporales para proforma_id:', proformaId);
        fetch(`/cronograma-sf/temporales/${proformaId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    // console.log('✅ Cuotas SF temporales encontradas:', data.data.length);
                    displayExistingCuotasSF(data.data, 'Temporales');
                } else {
                    // console.log('ℹ️ No hay cuotas SF existentes, generando cuota por defecto...');
                    hideExistingCuotasSFSection();
                    // Solo generar cuota por defecto si no hay cuotas existentes
                    const saldoFinanciar = parseFloat(document.getElementById('sf-montoTotal').value) || 0;
                    if (saldoFinanciar > 0) {
                        generateDefaultCuotas(saldoFinanciar);
                    }
                }
            })
            .catch(error => {
                // console.error('❌ Error al cargar cuotas SF temporales:', error);
                hideExistingCuotasSFSection();
                // En caso de error, también generar cuota por defecto
                const saldoFinanciar = parseFloat(document.getElementById('sf-montoTotal').value) || 0;
                if (saldoFinanciar > 0) {
                    generateDefaultCuotas(saldoFinanciar);
                }
            });
    }

    // Función para mostrar cuotas existentes SF
    function displayExistingCuotasSF(cuotas, tipo) {
        const section = document.getElementById('sf-cuotasExistentesSection');
        const tableBody = document.getElementById('sf-cuotasExistentesTableBody');
        
        if (!section || !tableBody) {
            // console.error('❌ No se encontraron elementos de cuotas existentes SF');
            return;
        }

        // Limpiar tabla
        tableBody.innerHTML = '';

        // Obtener datos de la primera cuota para preseleccionar campos del formulario
        if (cuotas.length > 0) {
            const primeraCuota = cuotas[0];
            
            // Preseleccionar campos del formulario con los datos existentes
            if (primeraCuota.tipo_comprobante_id) {
                const tipoComprobanteSelect = document.getElementById('sf-tipoComprobante');
                if (tipoComprobanteSelect) {
                    // Buscar la opción que coincida con el ID
                    for (let option of tipoComprobanteSelect.options) {
                        if (option.value == primeraCuota.tipo_comprobante_id) {
                            option.selected = true;
                            // console.log('✅ Tipo comprobante preseleccionado:', primeraCuota.tipo_comprobante);
                            break;
                        }
                    }
                }
            }
            
            if (primeraCuota.entidad_financiera) {
                const entidadFinancieraSelect = document.getElementById('sf-entidadFinanciera');
                if (entidadFinancieraSelect) {
                    // Buscar la opción que coincida con el texto
                    for (let option of entidadFinancieraSelect.options) {
                        if (option.text === primeraCuota.entidad_financiera) {
                            option.selected = true;
                            break;
                        }
                    }
                }
            }
            
            if (primeraCuota.tipo_financiamiento) {
                const tipoFinanciamientoSelect = document.getElementById('sf-tipoFinanciamiento');
                if (tipoFinanciamientoSelect) {
                    // Buscar la opción que coincida con el texto
                    for (let option of tipoFinanciamientoSelect.options) {
                        if (option.text === primeraCuota.tipo_financiamiento) {
                            option.selected = true;
                            break;
                        }
                    }
                }
            }
        }

        // Agregar cuotas a la tabla
        cuotas.forEach(cuota => {
            const row = document.createElement('tr');
            row.className = 'border-b border-gray-200 hover:bg-gray-50';
            
            const estadoClass = cuota.estado === 'Pagado' ? 'bg-green-100 text-green-800' : 
                               cuota.estado === 'Pendiente' ? 'bg-yellow-100 text-yellow-800' : 
                               'bg-red-100 text-red-800';

            row.innerHTML = `
                <td class="px-4 py-2 text-sm">${cuota.numero_cuota}</td>
                <td class="px-4 py-2 text-sm">${new Date(cuota.fecha_pago).toLocaleDateString('es-PE')}</td>
                <td class="px-4 py-2 text-sm font-medium">S/ ${parseFloat(cuota.monto).toLocaleString('es-PE', {minimumFractionDigits: 2})}</td>
                <td class="px-4 py-2 text-sm">${cuota.motivo || '-'}</td>
                <td class="px-4 py-2">
                    <span class="px-2 py-1 text-xs font-medium rounded-full ${estadoClass}">
                        ${cuota.estado}
                    </span>
                </td>
                <td class="px-4 py-2 text-sm">${cuota.entidad_financiera || '-'}</td>
                <td class="px-4 py-2 text-sm">${cuota.tipo_financiamiento || '-'}</td>
                <td class="px-4 py-2 text-sm">${cuota.tipo_comprobante || '-'}</td>
            `;
            
            tableBody.appendChild(row);
        });

        // Actualizar título de la sección
        const sectionTitle = section.querySelector('h3');
        if (sectionTitle) {
            sectionTitle.textContent = `Cuotas de Saldo a Financiar Existentes (${tipo})`;
        }

        // Mostrar la sección
        section.classList.remove('hidden');
        // console.log('✅ Cuotas SF existentes mostradas y campos preseleccionados');
    }

    // Función para ocultar sección de cuotas existentes SF
    function hideExistingCuotasSFSection() {
        const section = document.getElementById('sf-cuotasExistentesSection');
        if (section) {
            section.classList.add('hidden');
        }
    }

    // Función para resetear el formulario
    function resetSFForm() {
        document.getElementById('sf-fechaInicio').value = '';
        document.getElementById('sf-montoTotal').value = 0;
        document.getElementById('sf-numeroCuotas').value = 1;
        document.getElementById('sf-motivo').value = 'Saldo a financiar';
        document.getElementById('sf-tipoComprobante').value = '';
        document.getElementById('sf-entidadFinanciera').value = '';
        document.getElementById('sf-tipoFinanciamiento').value = '';
        document.getElementById('sf-bonoMiVivienda').checked = false;
        document.getElementById('sf-bonoVerde').checked = false;
        document.getElementById('sf-bonoIntegrador').checked = false;
        document.getElementById('sf-cuotasSection').classList.add('hidden');
        document.getElementById('sf-cuotasTableBody').innerHTML = '';
    }

    // Función para generar cuotas por defecto
    function generateDefaultCuotas(saldoFinanciar) {
        // Establecer valores por defecto
        const today = new Date();
        const formattedDate = today.toISOString().split('T')[0];
        document.getElementById('sf-fechaInicio').value = formattedDate;
        document.getElementById('sf-montoTotal').value = saldoFinanciar;
        document.getElementById('sf-numeroCuotas').value = 1;
        document.getElementById('sf-motivo').value = 'Saldo a financiar';
        
        // Generar la cuota automáticamente
        const tableBody = document.getElementById('sf-cuotasTableBody');
        tableBody.innerHTML = '';
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="border border-gray-300 px-4 py-2">1</td>
            <td class="border border-gray-300 px-4 py-2">
                <input type="date" value="${formattedDate}" 
                       class="w-full p-1 border border-gray-200 rounded">
            </td>
            <td class="border border-gray-300 px-4 py-2">
                <input type="number" value="${saldoFinanciar.toFixed(2)}" step="0.01" 
                       class="w-full p-1 border border-gray-200 rounded">
            </td>
            <td class="border border-gray-300 px-4 py-2">
                <input type="text" value="Saldo a financiar" 
                       class="w-full p-1 border border-gray-200 rounded">
            </td>
            <td class="border border-gray-300 px-4 py-2">Pendiente</td>
        `;
        tableBody.appendChild(row);
        
        // Mostrar la sección de cuotas
        document.getElementById('sf-cuotasSection').classList.remove('hidden');
        
        // Actualizar totales
        document.getElementById('sf-totalSaldoFinanciar').value = saldoFinanciar.toFixed(2);
        document.getElementById('sf-diferencia').value = '0.00';
    }

    // Event listener para generar cuotas SF
    document.getElementById('sf-generarCuotas').addEventListener('click', function() {
        const fechaInicio = document.getElementById('sf-fechaInicio').value;
        const montoTotal = parseFloat(document.getElementById('sf-montoTotal').value);
        const numeroCuotas = parseInt(document.getElementById('sf-numeroCuotas').value);
        const motivo = document.getElementById('sf-motivo').value;

        if (!fechaInicio || !montoTotal || !numeroCuotas || numeroCuotas < 1) {
            alert('Por favor, complete todos los campos requeridos');
            return;
        }

        // Verificar si hay cuotas existentes y ocultarlas al generar nuevas
        const existingCuotasSection = document.getElementById('sf-cuotasExistentesSection');
        if (existingCuotasSection && !existingCuotasSection.classList.contains('hidden')) {
            existingCuotasSection.classList.add('hidden');
            // console.log('🔄 Ocultando cuotas existentes para mostrar nuevas cuotas generadas');
        }

        const montoPorCuota = montoTotal / numeroCuotas;
        const tableBody = document.getElementById('sf-cuotasTableBody');
        tableBody.innerHTML = '';

        for (let i = 1; i <= numeroCuotas; i++) {
            const fechaCuota = new Date(fechaInicio);
            fechaCuota.setMonth(fechaCuota.getMonth() + (i - 1));
            
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="border border-gray-300 px-4 py-2">${i}</td>
                <td class="border border-gray-300 px-4 py-2">
                    <input type="date" value="${fechaCuota.toISOString().split('T')[0]}" 
                           class="w-full p-1 border border-gray-200 rounded">
                </td>
                <td class="border border-gray-300 px-4 py-2">
                    <input type="number" value="${montoPorCuota.toFixed(2)}" step="0.01" 
                           class="w-full p-1 border border-gray-200 rounded">
                </td>
                <td class="border border-gray-300 px-4 py-2">
                    <input type="text" value="${motivo}" 
                           class="w-full p-1 border border-gray-200 rounded">
                </td>
                <td class="border border-gray-300 px-4 py-2">Pendiente</td>
            `;
            tableBody.appendChild(row);
        }

        // Mostrar la sección de cuotas
        document.getElementById('sf-cuotasSection').classList.remove('hidden');
        
        // Actualizar totales
        document.getElementById('sf-totalSaldoFinanciar').value = montoTotal.toFixed(2);
        document.getElementById('sf-diferencia').value = '0.00';
        
        // console.log('✅ Nuevas cuotas SF generadas, tabla existente oculta');
    });

    // Event listener para aceptar saldo financiar
    document.getElementById('sf-aceptarSaldoFinanciar').addEventListener('click', function() {
        const cuotas = [];
        const rows = document.querySelectorAll('#sf-cuotasTableBody tr');
        
        if (rows.length === 0) {
            alert('Debe generar las cuotas primero');
            return;
        }

        // Validar campos requeridos
        const entidadFinanciera = document.getElementById('sf-entidadFinanciera').value;
        const tipoFinanciamiento = document.getElementById('sf-tipoFinanciamiento').value;
        
        if (!entidadFinanciera || !tipoFinanciamiento) {
            alert('Debe seleccionar la Entidad Financiera y el Tipo de Financiamiento');
            return;
        }

        rows.forEach((row, index) => {
            const fecha = row.querySelector('input[type="date"]').value;
            const monto = parseFloat(row.querySelector('input[type="number"]').value);
            const motivo = row.querySelector('input[type="text"]').value;
            
            if (fecha && monto && motivo) {
                cuotas.push({
                    numero_cuota: index + 1,
                    fecha_pago: fecha,
                    monto: monto,
                    motivo: motivo,
                    estado: 'Pendiente'
                });
            }
        });

        if (cuotas.length === 0) {
            alert('No hay cuotas válidas para procesar');
            return;
        }

        // Preparar datos para enviar
        const separacionId = getCurrentSeparacionId();
        const fechaInicio = document.getElementById('sf-fechaInicio').value;
        const montoTotal = parseFloat(document.getElementById('sf-montoTotal').value) || 0;
        const numeroCuotas = parseInt(document.getElementById('sf-numeroCuotas').value) || 1;
        
        // Validar campos requeridos
        if (!fechaInicio) {
            alert('La fecha de inicio es requerida');
            return;
        }
        
        const cronogramaSFData = {
            proforma_id: window.currentProformaId,
            fecha_inicio: fechaInicio,
            monto_total: montoTotal,
            saldo_financiar: montoTotal, // Usar el mismo valor por ahora
            numero_cuotas: numeroCuotas,
            banco_id: entidadFinanciera, // Corregido: era entidad_financiera_id
            tipo_financiamiento_id: tipoFinanciamiento,
            tipo_comprobante_id: document.getElementById('sf-tipoComprobante').value,
            bono_mivivienda: document.getElementById('sf-bonoMiVivienda').checked, // Corregido: era bono_mi_vivienda
            bono_verde: document.getElementById('sf-bonoVerde').checked,
            bono_integrador: document.getElementById('sf-bonoIntegrador').checked,
            cuotas: cuotas
        };

        // Solo incluir separacion_id si existe (no es null)
        if (separacionId) {
            cronogramaSFData.separacion_id = separacionId;
        }

        // Deshabilitar botón para evitar doble envío
        const button = document.getElementById('sf-aceptarSaldoFinanciar');
        button.disabled = true;
        button.textContent = 'Guardando...';

        // Enviar datos al servidor
        fetch('/cronograma-sf/guardar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(cronogramaSFData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Cronograma de Saldo a Financiar guardado exitosamente');
                closeCronogramaSFModal();
                
                // Recargar la página o actualizar la tabla si es necesario
                if (typeof window.refreshProformaData === 'function') {
                    window.refreshProformaData();
                }
            } else {
                alert('Error al guardar: ' + (data.message || 'Error desconocido'));
                console.error('Error del servidor:', data);
            }
        })
        .catch(error => {
            console.error('Error al guardar cronograma SF:', error);
            alert('Error de conexión al guardar el cronograma');
        })
        .finally(() => {
            // Rehabilitar botón
            button.disabled = false;
            button.textContent = 'Aceptar Saldo Financiar';
        });
    });
});
</script>

<style>
/* Estilos adicionales para el modal SF */
#cronograma-sf-modal {
    backdrop-filter: blur(4px);
}

#cronograma-sf-modal .max-h-96 {
    max-height: 24rem;
}

#cronograma-sf-modal .sticky {
    position: sticky;
}

#cronograma-sf-modal .top-0 {
    top: 0;
}
</style>
</div>