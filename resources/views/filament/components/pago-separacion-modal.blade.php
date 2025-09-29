{{-- Modal de Registro de Pago de Separación --}}
<div id="pago-separacion-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-5xl w-full max-h-[90vh] overflow-y-auto">
            {{-- Header --}}
            <div class="flex justify-between items-center p-6 border-b">
                <h2 class="text-xl font-semibold text-gray-800">Registro de Pago de Separación</h2>
                <button type="button" onclick="closePagoSeparacionModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-6">
                <form id="pago-separacion-form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="separacion_id" name="separacion_id" value="">
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Fecha de Pago --}}
                        <div>
                            <label for="fecha_pago" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Pago <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="fecha_pago" name="fecha_pago" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        {{-- Monto --}}
                        <div>
                            <label for="monto" class="block text-sm font-medium text-gray-700 mb-2">
                                Monto <span class="text-red-500">*</span>
                                <span id="monto-separacion-info" class="text-xs text-gray-500 ml-2">(Monto de Separación)</span>
                            </label>
                            <input type="number" id="monto" name="monto" step="0.01" min="0" required readonly
                                class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed"
                                placeholder="0.00">
                            <div id="monto-validation-message" class="text-xs text-red-500 mt-1 hidden"></div>
                        </div>

                        {{-- Tipo Moneda --}}
                        <div>
                            <label for="moneda_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo Moneda <span class="text-red-500">*</span>
                            </label>
                            <select id="moneda_id" name="moneda_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">--Seleccionar--</option>
                            </select>
                        </div>

                        {{-- Tipo de Cambio --}}
                        <div>
                            <label for="tipo_cambio" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Cambio
                            </label>
                            <input type="number" id="tipo_cambio" name="tipo_cambio" step="0.0001" min="0" value="3.70"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        {{-- Monto de Pago --}}
                        <div>
                            <label for="monto_pago" class="block text-sm font-medium text-gray-700 mb-2">
                                Monto de Pago <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="monto_pago" name="monto_pago" step="0.01" min="0" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="0.00">
                        </div>

                        {{-- Medio de Pago --}}
                        <div>
                            <label for="medio_pago_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Medio de Pago <span class="text-red-500">*</span>
                            </label>
                            <select id="medio_pago_id" name="medio_pago_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">--Seleccionar--</option>
                            </select>
                        </div>

                        {{-- Número de Cuenta Bancaria --}}
                        <div>
                            <label for="cuenta_bancaria_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Número de Cuenta Bancaria
                            </label>
                            <select id="cuenta_bancaria_id" name="cuenta_bancaria_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">--Seleccionar--</option>
                            </select>
                        </div>

                        {{-- Número de Operación --}}
                        <div>
                            <label for="numero_operacion" class="block text-sm font-medium text-gray-700 mb-2">
                                Número de Operación
                            </label>
                            <input type="text" id="numero_operacion" name="numero_operacion" maxlength="100"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Ingrese número de operación">
                        </div>

                        {{-- Número de Documento --}}
                        <div>
                            <label for="numero_documento" class="block text-sm font-medium text-gray-700 mb-2">
                                Número de Documento
                            </label>
                            <input type="text" id="numero_documento" name="numero_documento" maxlength="100"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Ingrese número de documento">
                        </div>

                        {{-- Agencia Bancaria --}}
                        <div>
                            <label for="agencia_bancaria" class="block text-sm font-medium text-gray-700 mb-2">
                                Agencia Bancaria
                            </label>
                            <input type="text" id="agencia_bancaria" name="agencia_bancaria" maxlength="200"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Ingrese agencia bancaria">
                        </div>

                        {{-- Archivo Comprobante --}}
                        <div>
                            <label for="archivo_comprobante" class="block text-sm font-medium text-gray-700 mb-2">
                                Archivo Comprobante
                            </label>
                            <input type="file" id="archivo_comprobante" name="archivo_comprobante" 
                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Formatos permitidos: PDF, JPG, PNG, DOC, DOCX (Max: 5MB)</p>
                        </div>
                    </div>

                    {{-- Observaciones --}}
                    <div class="mt-6">
                        <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                            Observaciones
                        </label>
                        <textarea id="observaciones" name="observaciones" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Ingrese observaciones adicionales..."></textarea>
                    </div>

                    {{-- Botón Agregar Pago --}}
                    <div class="mt-6 flex justify-end">
                        <button type="button" onclick="agregarPagoATabla()"
                            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors focus:outline-none focus:ring-2 focus:ring-green-500">
                            <i class="fas fa-plus mr-2"></i>Agregar a Lista
                        </button>
                    </div>
                </form>

                {{-- Tabla de Pagos Registrados --}}
                <div id="pagosSection" class="mt-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Lista de Pagos Registrados</h3>
                    
                    {{-- Resumen de Pagos --}}
                    <div id="resumen-pagos" class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg" style="display: none;">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-600">Monto Total:</span>
                                <div id="monto-total" class="text-lg font-bold text-blue-600">S/ 0.00</div>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">Total Pagado:</span>
                                <div id="total-pagado" class="text-lg font-bold text-green-600">S/ 0.00</div>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">Saldo Pendiente:</span>
                                <div id="saldo-pendiente" class="text-lg font-bold text-red-600">S/ 0.00</div>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">Estado:</span>
                                <div id="estado-pago" class="text-lg font-bold">Pendiente</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Pago</th>
                                  <!--  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>-->
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Moneda</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo Cambio</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto Pago</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medio Pago</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nº Operación</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="pagos-table-body" class="bg-white divide-y divide-gray-200">
                                <tr id="no-pagos-row">
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                        No hay pagos registrados
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex justify-end space-x-3 p-6 border-t bg-gray-50">
                <button type="button" onclick="closePagoSeparacionModal()" 
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition-colors">
                    Cancelar
                </button>
                <button type="button" id="guardar-pagos-btn" onclick="guardarTodosLosPagos()"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                    Guardar Pagos
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Event listener para abrir el modal
    window.addEventListener('open-modal', function(event) {
        if (event.detail && event.detail.id === 'pago-separacion-modal') {
            const separacionId = event.detail.separacionId;
            openPagoSeparacionModal(separacionId);
        }
    });

    // Función para abrir el modal
    window.openPagoSeparacionModal = function(separacionId) {
        const modal = document.getElementById('pago-separacion-modal');
        if (modal) {
            modal.classList.remove('hidden');
            
            // Establecer el separacion_id en el campo oculto
            if (separacionId && separacionId !== 'null') {
                document.getElementById('separacion_id').value = separacionId;
            }
            
            // Cargar datos iniciales
            setTimeout(() => {
                loadInitialData();
                setDefaultDate();
                loadExistingPagos();
                
                // Cargar información de la separación si tenemos el ID
                if (separacionId && separacionId !== 'null') {
                    loadSeparacionInfo(separacionId);
                }
            }, 100);
        }
    };

    // Función para cerrar el modal
    window.closePagoSeparacionModal = function() {
        const modal = document.getElementById('pago-separacion-modal');
        if (modal) {
            modal.classList.add('hidden');
            resetForm();
        }
    };

    // Función para cargar datos iniciales
    function loadInitialData() {
        console.log('=== CARGANDO DATOS INICIALES PAGO SEPARACIÓN ===');
        
        // Obtener ID de separación
        const separacionId = getCurrentSeparacionId();
        if (separacionId) {
            document.getElementById('separacion_id').value = separacionId;
            console.log('✓ Separación ID establecido:', separacionId);
            
            // Si es un ID temporal (empieza con "temp_"), cargar datos de proforma
            if (separacionId.startsWith('temp_')) {
                console.log('🔄 ID temporal detectado, cargando datos de proforma...');
                loadProformaData();
            } else {
                // Cargar información de la separación real
                loadSeparacionInfo(separacionId);
            }
        } else {
            console.log('❌ No se pudo encontrar el ID de la separación');
            // Si no hay separación ID, intentar cargar datos de la proforma
            loadProformaData();
        }

        // Cargar monedas
        loadMonedas();
        
        // Cargar medios de pago
        loadMediosPago();
        
        // Cargar cuentas bancarias
        loadCuentasBancarias();
    }

    // Función para cargar monedas
    function loadMonedas() {
        fetch('/api/monedas')
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('moneda_id');
                select.innerHTML = '<option value="">--Seleccionar--</option>';
                
                if (data.success && data.data) {
                    data.data.forEach(moneda => {
                        const option = document.createElement('option');
                        option.value = moneda.id;
                        option.textContent = moneda.nombre;
                        select.appendChild(option);
                    });
                    console.log('✓ Monedas cargadas:', data.data.length);
                }
            })
            .catch(error => {
                console.error('Error al cargar monedas:', error);
            });
    }

    // Función para cargar medios de pago
    function loadMediosPago() {
        fetch('/api/medios-pago')
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('medio_pago_id');
                select.innerHTML = '<option value="">--Seleccionar--</option>';
                
                if (data.success && data.data) {
                    data.data.forEach(medio => {
                        const option = document.createElement('option');
                        option.value = medio.id;
                        option.textContent = medio.nombre;
                        select.appendChild(option);
                    });
                    console.log('✓ Medios de pago cargados:', data.data.length);
                }
            })
            .catch(error => {
                console.error('Error al cargar medios de pago:', error);
            });
    }

    // Función para cargar cuentas bancarias
    function loadCuentasBancarias() {
        fetch('/api/cuentas-bancarias')
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('cuenta_bancaria_id');
                select.innerHTML = '<option value="">--Seleccionar--</option>';
                
                if (data.success && data.data) {
                    data.data.forEach(cuenta => {
                        const option = document.createElement('option');
                        option.value = cuenta.id;
                        option.textContent = `${cuenta.banco} ${cuenta.moneda}/ ${cuenta.numero_cuenta}`;
                        select.appendChild(option);
                    });
                    console.log('✓ Cuentas bancarias cargadas:', data.data.length);
                }
            })
            .catch(error => {
                console.error('Error al cargar cuentas bancarias:', error);
            });
    }

    // Función para cargar información de la separación
    function loadSeparacionInfo(separacionId) {
        fetch(`/api/separacion/${separacionId}/info`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    const separacionInfo = data.data;
                    const montoSeparacion = parseFloat(separacionInfo.monto_separacion) || 0;
                    
                    // Mostrar información del monto de separación
                    const montoInfo = document.getElementById('monto-separacion-info');
                    montoInfo.textContent = `(Monto separación: S/ ${montoSeparacion.toFixed(2)})`;
                    
                    // Establecer el monto de separación como valor por defecto
                    const montoInput = document.getElementById('monto');
                    if (!montoInput.value) {
                        montoInput.value = montoSeparacion.toFixed(2);
                        calculateMontoPago(); // Recalcular monto de pago
                    }
                    
                    // Guardar el monto de separación para validaciones
                    window.montoSeparacionActual = montoSeparacion;
                    
                    console.log('✓ Información de separación cargada:', separacionInfo);
                }
            })
            .catch(error => {
                console.error('Error al cargar información de separación:', error);
            });
    }

    // Función para establecer fecha por defecto
    function setDefaultDate() {
        const fechaPago = document.getElementById('fecha_pago');
        if (!fechaPago.value) {
            const today = new Date().toISOString().split('T')[0];
            fechaPago.value = today;
        }
    }

    // Función para calcular monto de pago automáticamente
    document.getElementById('monto').addEventListener('input', calculateMontoPago);
    document.getElementById('tipo_cambio').addEventListener('input', calculateMontoPago);

    function calculateMontoPago() {
        const monto = parseFloat(document.getElementById('monto').value) || 0;
        const monedaSelect = document.getElementById('moneda_id');
        const monedaNombre = monedaSelect.options[monedaSelect.selectedIndex]?.text || '';
        const tipoCambio = parseFloat(document.getElementById('tipo_cambio').value) || 3.7;
        
        let montoPago = monto;
        
        // Si la moneda es dólares (USD), aplicar tipo de cambio
        if (monedaNombre.toLowerCase().includes('dólar') || monedaNombre.toLowerCase().includes('dollar') || monedaNombre.toLowerCase().includes('usd')) {
            montoPago = monto * tipoCambio;
        }
        // Si es soles (PEN), mantener el monto original
        
        document.getElementById('monto_pago').value = montoPago.toFixed(2);
    }

    // Función para agregar pago
    window.agregarPago = function() {
        const form = document.getElementById('pago-separacion-form');
        const formData = new FormData(form);
        
        // Validar campos requeridos
        if (!validateForm()) {
            return;
        }

        const button = document.getElementById('agregar-pago-btn');
        button.disabled = true;
        button.textContent = 'Guardando...';

        fetch('/api/pagos-separacion', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('✅ Pago registrado:', data);
                alert('Pago registrado exitosamente');
                
                // Limpiar formulario
                resetFormFields();
                
                // Recargar tabla de pagos
                loadExistingPagos();
            } else {
                alert('Error al registrar pago: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('❌ Error al registrar pago:', error);
            alert('Error al registrar el pago: ' + error.message);
        })
        .finally(() => {
            button.disabled = false;
            button.textContent = 'Agregar Pago';
        });
    };

    // Función para validar formulario
    function validateForm() {
        const requiredFields = ['fecha_pago', 'monto', 'moneda_id', 'monto_pago', 'medio_pago_id'];
        
        for (let field of requiredFields) {
            const element = document.getElementById(field);
            if (!element.value.trim()) {
                alert(`El campo ${element.previousElementSibling.textContent.replace('*', '').trim()} es requerido`);
                element.focus();
                return false;
            }
        }

        // Validar que el monto no exceda el monto de separación
        const monto = parseFloat(document.getElementById('monto').value) || 0;
        const montoSeparacion = window.montoSeparacionActual || 0;
        const validationMessage = document.getElementById('monto-validation-message');
        
        if (monto > montoSeparacion && montoSeparacion > 0) {
            validationMessage.textContent = `El monto no puede exceder el monto de separación (S/ ${montoSeparacion.toFixed(2)})`;
            validationMessage.classList.remove('hidden');
            document.getElementById('monto').focus();
            return false;
        } else {
            validationMessage.classList.add('hidden');
        }

        // Validar archivo si se seleccionó
        const archivo = document.getElementById('archivo_comprobante');
        if (archivo.files.length > 0) {
            const file = archivo.files[0];
            const maxSize = 5 * 1024 * 1024; // 5MB
            
            if (file.size > maxSize) {
                alert('El archivo no puede ser mayor a 5MB');
                return false;
            }
        }

        return true;
    }

    // Función para cargar pagos existentes
    function loadExistingPagos() {
        console.log('=== CARGANDO PAGOS EXISTENTES ===');
        
        const proformaId = getCurrentProformaId();
        
        if (!proformaId) {
            console.log('⚠️ No se encontró proforma_id');
            // Mostrar sección vacía pero visible
            displayPagos({ success: true, data: [] });
            return;
        }
        
        console.log('🔍 Proforma ID encontrado:', proformaId);
        
        // PRIMERO: Intentar cargar pagos definitivos (pagos que ya tienen separacion_id)
        const separacionId = getCurrentSeparacionId();
        
        if (separacionId && !separacionId.startsWith('temp_')) {
            console.log('🔍 Buscando pagos definitivos para separacion_id:', separacionId);
            
            fetch(`/api/pagos-separacion/${separacionId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('📦 Pagos definitivos recibidos:', data);
                    
                    if (data.success && data.data && data.data.length > 0) {
                        console.log('✅ Mostrando', data.data.length, 'pagos definitivos');
                        displayPagos(data);
                        return; // Salir aquí si encontramos pagos definitivos
                    } else {
                        console.log('ℹ️ No hay pagos definitivos, buscando pagos por proforma...');
                        loadPagosByProforma(proformaId);
                    }
                })
                .catch(error => {
                    console.error('❌ Error al cargar pagos definitivos:', error);
                    // Si falla, intentar cargar por proforma
                    loadPagosByProforma(proformaId);
                });
        } else {
            console.log('ℹ️ No hay separacion_id válido, buscando pagos por proforma...');
            loadPagosByProforma(proformaId);
        }
    }
    
    // Función auxiliar para cargar pagos por proforma
    function loadPagosByProforma(proformaId) {
        console.log('🔍 Buscando pagos por proforma_id:', proformaId);
        
        fetch(`/api/pagos-separacion/proforma/${proformaId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('📦 Pagos por proforma recibidos:', data);
                
                if (data.success && data.data && data.data.length > 0) {
                    console.log('✅ Mostrando', data.data.length, 'pagos por proforma');
                    displayPagos(data);
                } else {
                    console.log('ℹ️ No hay pagos por proforma');
                    // Mostrar sección vacía pero visible
                    displayPagos({ success: true, data: [] });
                }
            })
            .catch(error => {
                console.error('❌ Error al cargar pagos por proforma:', error);
                // Si falla, mostrar sección vacía pero visible
                displayPagos({ success: true, data: [] });
            });
    }

    // Función auxiliar para mostrar los pagos en la tabla
    function displayPagos(data) {
        console.log('📊 Mostrando pagos en tabla:', data);
        
        const tbody = document.getElementById('pagos-table-body');
        const noDataRow = document.getElementById('no-pagos-row');
        const resumenPagos = document.getElementById('resumen-pagos');
        const pagosSection = document.getElementById('pagosSection');
        
        // Asegurar que la sección de pagos siempre esté visible
        if (pagosSection) {
            pagosSection.classList.remove('hidden');
            console.log('👁️ Sección de pagos mostrada');
        }
        
        if (data.success && data.data && data.data.length > 0) {
            console.log('✅ Mostrando', data.data.length, 'pagos');
            
            // Ocultar fila de "no hay datos"
            noDataRow.style.display = 'none';
            
            // Mostrar resumen de pagos
            resumenPagos.style.display = 'block';
            
            // Actualizar resumen con datos del backend
            if (data.resumen) {
                console.log('📈 Actualizando resumen:', data.resumen);
                
                // Guardar datos globalmente para uso en pagos temporales
                window.resumenPagosGlobal = data.resumen;
                window.pagosExistentesGlobales = data.data;
                
                // Usar los campos correctos según el tipo de respuesta
                const montoTotal = data.resumen.monto_total || data.resumen.monto_total_proforma || data.resumen.monto_separacion || 0;
                const totalPagado = data.resumen.total_pagado || data.resumen.total_pagos_realizados || 0;
                const saldoPendiente = data.resumen.saldo_pendiente || 0;
                
                document.getElementById('monto-total').textContent = formatCurrency(montoTotal);
                document.getElementById('total-pagado').textContent = formatCurrency(totalPagado);
                document.getElementById('saldo-pendiente').textContent = formatCurrency(saldoPendiente);
                
                // Actualizar estado
                const estadoElement = document.getElementById('estado-pago');
                if (saldoPendiente <= 0) {
                    estadoElement.textContent = 'Pagado';
                    estadoElement.className = 'text-lg font-bold text-green-600';
                } else {
                    estadoElement.textContent = 'Pendiente';
                    estadoElement.className = 'text-lg font-bold text-red-600';
                }
            }
            
            // Limpiar filas existentes (excepto la de "no hay datos")
            const existingRows = tbody.querySelectorAll('tr:not(#no-pagos-row)');
            existingRows.forEach(row => row.remove());
            
            // Agregar filas de pagos
            data.data.forEach((pago, index) => {
                console.log('📝 Creando fila para pago:', pago);
                const row = createPagoRow(pago);
                tbody.appendChild(row);
            });
        } else {
            console.log('📭 No hay pagos para mostrar');
            
            // Mostrar fila de "no hay datos"
            noDataRow.style.display = 'table-row';
            
            // Ocultar resumen de pagos
            resumenPagos.style.display = 'none';
            
            // Limpiar filas existentes
            const existingRows = tbody.querySelectorAll('tr:not(#no-pagos-row)');
            existingRows.forEach(row => row.remove());
        }
    }

    // Función para crear fila de pago
    function createPagoRow(pago, saldoPendiente = null) {
        const row = document.createElement('tr');
        
        // Crear icono de descarga si hay archivo comprobante
        const iconoDescarga = pago.archivo_comprobante ? 
            `<a href="/api/pagos-separacion/comprobante/${pago.id}" 
               target="_blank" 
               class="text-blue-600 hover:text-blue-800 mr-2" 
               title="Descargar comprobante">
                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
            </a>` : '';
        
        row.innerHTML = `
            <td class="px-4 py-3 text-sm text-gray-900">${formatDate(pago.fecha_pago)}</td>
            <td class="px-4 py-3 text-sm text-gray-900">${pago.moneda_nombre || pago.moneda?.nombre || 'N/A'}</td>
            <td class="px-4 py-3 text-sm text-gray-900">${pago.tipo_cambio}</td>
            <td class="px-4 py-3 text-sm text-gray-900">${formatCurrency(pago.monto_pago || 0)}</td>
            <td class="px-4 py-3 text-sm text-gray-900">${pago.medio_pago_nombre || pago.medio_pago?.nombre || 'N/A'}</td>
            <td class="px-4 py-3 text-sm text-gray-900">
                ${iconoDescarga}
                ${pago.numero_operacion || 'N/A'}
            </td>
            <td class="px-4 py-3 text-sm text-gray-900">
                <button onclick="eliminarPago(${pago.id})" 
                    class="text-red-600 hover:text-red-800 text-sm">
                    Eliminar
                </button>
            </td>
        `;
        return row;
    }

    // Array para almacenar pagos temporales antes de guardar
    let pagosTemporales = [];

    // Función para agregar pago a la tabla temporal
    window.agregarPagoATabla = function() {
        console.log('🔄 Agregando pago a la tabla...');
        
        // Validar formulario
        if (!validarFormulario()) {
            return;
        }
        
        // Obtener datos del formulario
        const pagoData = obtenerDatosFormulario();
        
        // Agregar ID temporal único
        pagoData.temp_id = Date.now() + Math.random();
        pagoData.es_temporal = true;
        
        // Agregar a array temporal
        pagosTemporales.push(pagoData);
        
        // Actualizar tabla
        actualizarTablaConPagosTemporales();
        
        // Limpiar formulario
        resetFormFields();
        
        console.log('✅ Pago agregado a la tabla temporal');
    };

    // Función para validar formulario
    function validarFormulario() {
        const requiredFields = [
            { id: 'fecha_pago', name: 'Fecha de Pago' },
            { id: 'monto_pago', name: 'Monto de Pago' },
            { id: 'moneda_id', name: 'Moneda' },
            { id: 'medio_pago_id', name: 'Medio de Pago' }
        ];
        
        for (const field of requiredFields) {
            const element = document.getElementById(field.id);
            if (!element || !element.value || element.value.trim() === '') {
                alert(`El campo ${field.name} es obligatorio`);
                element?.focus();
                return false;
            }
        }
        
        // Validar monto de pago
        const montoPago = parseFloat(document.getElementById('monto_pago').value);
        if (isNaN(montoPago) || montoPago <= 0) {
            alert('El monto de pago debe ser un número mayor a 0');
            document.getElementById('monto_pago').focus();
            return false;
        }
        
        return true;
    }

    // Función para obtener datos del formulario
    function obtenerDatosFormulario() {
        const monedaSelect = document.getElementById('moneda_id');
        const medioPagoSelect = document.getElementById('medio_pago_id');
        const cuentaBancariaSelect = document.getElementById('cuenta_bancaria_id');
        const archivoInput = document.getElementById('archivo_comprobante');
        const monto = parseFloat(document.getElementById('monto').value);
        const tipoCambio = parseFloat(document.getElementById('tipo_cambio').value) || 3.7;
        const montoPago = parseFloat(document.getElementById('monto_pago').value) || 0;
        const monedaNombre = monedaSelect.options[monedaSelect.selectedIndex]?.text || '';
        
        // Calcular monto convertido
        let montoConvertido = monto;
        if (monedaNombre.toLowerCase().includes('dólar') || monedaNombre.toLowerCase().includes('dollar') || monedaNombre.toLowerCase().includes('usd')) {
            montoConvertido = monto * tipoCambio;
        }
        
        // Obtener información del archivo
        let archivoInfo = null;
        if (archivoInput.files.length > 0) {
            const archivo = archivoInput.files[0];
            archivoInfo = {
                name: archivo.name,
                size: archivo.size,
                type: archivo.type,
                file: archivo // Mantener referencia al archivo
            };
        }
        
        return {
            fecha_pago: document.getElementById('fecha_pago').value,
            monto: monto,
            moneda_id: monedaSelect.value,
            moneda_nombre: monedaNombre,
            tipo_cambio: tipoCambio,
            monto_pago: montoPago,
            monto_convertido: montoConvertido,
            medio_pago_id: medioPagoSelect.value,
            medio_pago_nombre: medioPagoSelect.options[medioPagoSelect.selectedIndex]?.text || 'N/A',
            cuenta_bancaria_id: cuentaBancariaSelect.value || null,
            numero_operacion: document.getElementById('numero_operacion').value || '',
            numero_documento: document.getElementById('numero_documento').value || '',
            agencia_bancaria: document.getElementById('agencia_bancaria').value || '',
            observaciones: document.getElementById('observaciones').value || '',
            archivo_comprobante_info: archivoInfo
        };
    }

    // Función para actualizar tabla con pagos temporales
    function actualizarTablaConPagosTemporales() {
        const tbody = document.getElementById('pagos-table-body');
        const noDataRow = document.getElementById('no-pagos-row');
        const resumenPagos = document.getElementById('resumen-pagos');
        
        // Obtener pagos existentes (ya guardados) si los hay
        const pagosExistentes = window.pagosExistentesGlobales || [];
        const todosLosPagos = [...pagosExistentes, ...pagosTemporales];
        
        // Limpiar tabla
        const existingRows = tbody.querySelectorAll('tr:not(#no-pagos-row)');
        existingRows.forEach(row => row.remove());
        
        if (todosLosPagos.length > 0) {
            noDataRow.style.display = 'none';
            
            // Mostrar resumen si hay datos del backend
            if (window.resumenPagosGlobal) {
                resumenPagos.style.display = 'block';
                
                // Calcular totales incluyendo pagos temporales
                const totalPagosTemporales = pagosTemporales.reduce((sum, pago) => sum + parseFloat(pago.monto_pago), 0);
                const totalPagadoActualizado = window.resumenPagosGlobal.total_pagado + totalPagosTemporales;
                const saldoPendienteActualizado = window.resumenPagosGlobal.monto_total - totalPagadoActualizado;
                
                document.getElementById('monto-total').textContent = formatCurrency(window.resumenPagosGlobal.monto_total);
                document.getElementById('total-pagado').textContent = formatCurrency(totalPagadoActualizado);
                document.getElementById('saldo-pendiente').textContent = formatCurrency(saldoPendienteActualizado);
                
                // Actualizar estado
                const estadoElement = document.getElementById('estado-pago');
                if (saldoPendienteActualizado <= 0) {
                    estadoElement.textContent = 'Pagado';
                    estadoElement.className = 'text-lg font-bold text-green-600';
                } else {
                    estadoElement.textContent = 'Pendiente';
                    estadoElement.className = 'text-lg font-bold text-red-600';
                }
            }
            
            // Agregar filas de pagos existentes con saldo calculado
            let montoTotal = window.resumenPagosGlobal ? window.resumenPagosGlobal.monto_total : 0;
            let saldoAcumulado = 0;
            
            // Primero los pagos existentes
            pagosExistentes.forEach((pago, index) => {
                saldoAcumulado += parseFloat(pago.monto_pago);
                const saldoPendiente = montoTotal - saldoAcumulado;
                const row = createPagoRow(pago, saldoPendiente);
                tbody.appendChild(row);
            });
            
            // Luego los pagos temporales
            pagosTemporales.forEach(pago => {
                saldoAcumulado += parseFloat(pago.monto_pago);
                const saldoPendiente = montoTotal - saldoAcumulado;
                const row = createPagoTemporalRow(pago, saldoPendiente);
                tbody.appendChild(row);
            });
        } else {
            noDataRow.style.display = 'table-row';
            resumenPagos.style.display = 'none';
        }
    }

    // Función para crear fila de pago temporal
    function createPagoTemporalRow(pago, saldoPendiente = null) {
        const row = document.createElement('tr');
        row.className = 'bg-yellow-50'; // Destacar que es temporal
        
        // Si se proporciona saldo pendiente, usarlo; sino usar el monto del pago
        const montoAMostrar = saldoPendiente !== null ? saldoPendiente : pago.monto_pago;
        
        row.innerHTML = `
            <td class="px-4 py-3 text-sm text-gray-900">${formatDate(pago.fecha_pago)}</td>
            <td class="px-4 py-3 text-sm text-gray-900">${pago.moneda_nombre}</td>
            <td class="px-4 py-3 text-sm text-gray-900">${pago.tipo_cambio}</td>
            <td class="px-4 py-3 text-sm text-gray-900">${formatCurrency(pago.monto_pago)}</td>
            <td class="px-4 py-3 text-sm text-gray-900">${pago.medio_pago_nombre}</td>
            <td class="px-4 py-3 text-sm text-gray-900">${pago.numero_operacion || 'N/A'}</td>
            <td class="px-4 py-3 text-sm text-gray-900">
                <button onclick="eliminarPagoTemporal('${pago.temp_id}')" 
                    class="text-red-600 hover:text-red-800 text-sm">
                    Eliminar
                </button>
            </td>
        `;
        return row;
    }

    // Función para eliminar pago temporal
    window.eliminarPagoTemporal = function(tempId) {
        if (!confirm('¿Está seguro de eliminar este pago de la lista?')) {
            return;
        }
        
        // Remover del array
        pagosTemporales = pagosTemporales.filter(pago => pago.temp_id !== tempId);
        
        // Actualizar tabla
        actualizarTablaConPagosTemporales();
        
        console.log('✅ Pago temporal eliminado');
    };

    // Función para guardar todos los pagos
    window.guardarTodosLosPagos = function() {
        if (pagosTemporales.length === 0) {
            alert('No hay pagos para guardar. Agregue al menos un pago a la lista.');
            return;
        }
        
        if (!confirm(`¿Está seguro de guardar ${pagosTemporales.length} pago(s)?`)) {
            return;
        }
        
        console.log('🚀 Guardando todos los pagos...');
        
        // Obtener separación ID y proforma ID
        const separacionId = getCurrentSeparacionId();
        const proformaId = getCurrentProformaId();
        
        // Validar que al menos uno de los IDs esté disponible
        if (!separacionId && !proformaId) {
            alert('No se pudo obtener el ID de la separación ni de la proforma');
            return;
        }
        
        // Crear FormData para manejar archivos
        const formData = new FormData();
        
        // Agregar datos básicos
        formData.append('separacion_id', separacionId && !separacionId.startsWith('temp_') ? separacionId : '');
        formData.append('proforma_id', proformaId || '');
        
        // Preparar datos de pagos y archivos
        const pagosParaEnvio = [];
        pagosTemporales.forEach((pago, index) => {
            // Remover campos temporales y archivo info
            const { temp_id, es_temporal, moneda_nombre, medio_pago_nombre, archivo_comprobante_info, ...pagoLimpio } = pago;
            
            // Agregar pago a la lista
            pagosParaEnvio.push(pagoLimpio);
            
            // Si hay archivo, agregarlo al FormData
            if (archivo_comprobante_info && archivo_comprobante_info.file) {
                formData.append(`archivo_comprobante_${index}`, archivo_comprobante_info.file);
            }
        });
        
        // Agregar pagos como JSON
        formData.append('pagos', JSON.stringify(pagosParaEnvio));
        
        // Agregar token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            formData.append('_token', csrfToken);
        }
        
        console.log('📤 Datos a enviar:', {
            separacion_id: separacionId,
            proforma_id: proformaId,
            pagos: pagosParaEnvio,
            archivos: pagosTemporales.filter(p => p.archivo_comprobante_info).length
        });
        
        // Enviar a la API
        fetch('/api/pagos-separacion/batch', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken || ''
            }
        })
        .then(response => {
            console.log('📥 Respuesta recibida, status:', response.status);
            
            // Verificar si la respuesta es JSON válida
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                console.error('❌ Respuesta no es JSON válida, content-type:', contentType);
                return response.text().then(text => {
                    console.error('❌ Contenido de respuesta:', text.substring(0, 500));
                    throw new Error('La respuesta del servidor no es JSON válida');
                });
            }
            
            return response.json();
        })
        .then(data => {
            console.log('✅ Datos JSON parseados:', data);
            
            if (data.success) {
                alert('Pagos guardados exitosamente');
                
                // Limpiar pagos temporales
                pagosTemporales = [];
                
                // Recargar pagos desde la BD
                loadExistingPagos();
                
                // Cerrar modal
                closePagoSeparacionModal();
            } else {
                alert('Error al guardar pagos: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error al guardar pagos:', error);
            alert('Error al guardar los pagos: ' + error.message);
        });
    };

    // Función para eliminar pago
    window.eliminarPago = function(pagoId) {
        if (!confirm('¿Está seguro de eliminar este pago?')) {
            return;
        }

        fetch(`/api/pagos-separacion/${pagoId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Pago eliminado exitosamente');
                loadExistingPagos();
            } else {
                alert('Error al eliminar pago: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error al eliminar pago:', error);
            alert('Error al eliminar el pago');
        });
    };

    // Función para resetear formulario
    function resetForm() {
        resetFormFields();
        loadExistingPagos();
    }

    function resetFormFields() {
        // Guardar el valor del monto antes del reset
        const montoValue = document.getElementById('monto').value;
        
        document.getElementById('pago-separacion-form').reset();
        document.getElementById('monto_pago').value = '';
        
        // Restaurar el valor del monto después del reset
        document.getElementById('monto').value = montoValue;
        
        setDefaultDate();
    }

    // Función para obtener ID de separación actual
    function getCurrentSeparacionId() {
        console.log('🔍 getCurrentSeparacionId: Iniciando búsqueda...');
        let separacionId = null;
        
        // Estrategia 1: Buscar en URL
        const pathParts = window.location.pathname.split('/');
        const separacionIndex = pathParts.indexOf('separacions');
        if (separacionIndex !== -1 && pathParts[separacionIndex + 1] && pathParts[separacionIndex + 1] !== 'create') {
            separacionId = pathParts[separacionIndex + 1];
            console.log('🔍 Separación ID encontrado en ruta:', separacionId);
            return separacionId;
        }

        // Estrategia 2: Buscar en parámetros URL (para casos de separación definitiva)
        const urlParams = new URLSearchParams(window.location.search);
        const from = urlParams.get('from');
        if (from === 'separacion_definitiva') {
            // En proceso de creación de separación definitiva, usar proforma_id como referencia
            const proformaId = urlParams.get('proforma_id');
            if (proformaId) {
                console.log('🔍 Proceso de separación definitiva detectado, usando proforma_id:', proformaId);
                return 'temp_' + proformaId; // ID temporal para separación en proceso
            }
        }

        // Estrategia 3: Buscar en elementos del DOM
        const separacionElement = document.querySelector('[data-separacion-id]');
        if (separacionElement) {
            separacionId = separacionElement.getAttribute('data-separacion-id');
            console.log('🔍 Separación ID encontrado en DOM:', separacionId);
            return separacionId;
        }

        // Estrategia 4: Buscar en componentes Livewire
        if (window.Livewire && window.Livewire.components) {
            try {
                for (let component of Object.values(window.Livewire.components.componentsById)) {
                    if (component.data && component.data.record && component.data.record.id) {
                        separacionId = component.data.record.id;
                        console.log('🔍 Separación ID encontrado en Livewire:', separacionId);
                        return separacionId;
                    }
                }
            } catch (error) {
                console.warn('⚠️ Error al buscar en componentes Livewire:', error);
            }
        }

        // Estrategia 5: Buscar en variables globales
        if (typeof window.separacionId !== 'undefined') {
            console.log('🔍 Separación ID encontrado en variable global:', window.separacionId);
            return window.separacionId;
        }

        console.log('❌ No se pudo encontrar el ID de la separación');
        return null;
    }

    // Función para obtener ID de proforma actual
    function getCurrentProformaId() {
        console.log('🔍 getCurrentProformaId: Iniciando búsqueda...');
        
        let proformaId = null;
        
        // Estrategia 1: Buscar en URL
        const pathParts = window.location.pathname.split('/');
        const proformaIndex = pathParts.indexOf('proformas');
        if (proformaIndex !== -1 && pathParts[proformaIndex + 1] && pathParts[proformaIndex + 1] !== 'create') {
            proformaId = pathParts[proformaIndex + 1];
            console.log('🔍 Proforma ID encontrado en ruta:', proformaId);
            return proformaId;
        }
        
        // Estrategia 2: Buscar en parámetros de URL
        const urlParams = new URLSearchParams(window.location.search);
        proformaId = urlParams.get('proforma_id');
        if (proformaId) {
            console.log('🔍 Proforma ID encontrado en parámetros URL:', proformaId);
            return proformaId;
        }
        
        // Estrategia 3: Buscar en elementos del DOM
        const proformaElement = document.querySelector('[data-proforma-id]');
        if (proformaElement) {
            proformaId = proformaElement.getAttribute('data-proforma-id');
            console.log('🔍 Proforma ID encontrado en DOM:', proformaId);
            return proformaId;
        }
        
        // Estrategia 4: Buscar en campos de input
        const proformaInput = document.querySelector('input[name="proforma_id"]');
        if (proformaInput && proformaInput.value) {
            proformaId = proformaInput.value;
            console.log('🔍 Proforma ID encontrado en input:', proformaId);
            return proformaId;
        }
        
        // Estrategia 5: Buscar en variables globales
        if (typeof window.proformaId !== 'undefined') {
            console.log('🔍 Proforma ID encontrado en variable global:', window.proformaId);
            return window.proformaId;
        }
        
        console.log('❌ No se pudo encontrar el ID de la proforma');
        return null;
    }

    // Funciones de utilidad
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('es-PE');
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('es-PE', {
            style: 'currency',
            currency: 'PEN',
            minimumFractionDigits: 2
        }).format(amount);
    }
});

// Función para cargar datos de la proforma
function loadProformaData() {
    console.log('=== INICIANDO loadProformaData ===');
    
    // Obtener datos directamente del DOM de la página
    const proformaData = extractProformaDataFromDOM();
    
    if (proformaData && proformaData.monto_separacion) {
        console.log('✅ Datos de proforma obtenidos del DOM:', proformaData);
        updateModalWithProformaData(proformaData);
    } else {
        console.warn('⚠️ No se pudieron obtener los datos de la proforma del DOM');
    }
}

// Función para extraer datos de la proforma del DOM
function extractProformaDataFromDOM() {
    console.log('🔍 Extrayendo datos del DOM...');
    
    const data = {};
    
    // Estrategia 1: Buscar en campos de input por name
    const montoSeparacionInput = document.querySelector('input[name="monto_separacion"]');
    if (montoSeparacionInput && montoSeparacionInput.value) {
        data.monto_separacion = montoSeparacionInput.value;
        console.log('✓ Monto separación encontrado en input:', data.monto_separacion);
    }
    
    // Estrategia 2: Buscar en campos de texto que contengan el valor
    if (!data.monto_separacion) {
        // Buscar elementos que contengan "1000.00" (el valor visible en la imagen)
        const elements = document.querySelectorAll('input, span, div');
        for (const element of elements) {
            const value = element.value || element.textContent || element.innerText;
            if (value && value.trim() === '1000.00') {
                // Verificar si el elemento está relacionado con "Monto de Separación"
                const label = element.closest('.grid')?.querySelector('label');
                if (label && label.textContent.includes('Monto de Separación')) {
                    data.monto_separacion = value.trim();
                    console.log('✓ Monto separación encontrado en elemento:', data.monto_separacion);
                    break;
                }
            }
        }
    }
    
    // Estrategia 3: Buscar por texto del label
    if (!data.monto_separacion) {
        const labels = document.querySelectorAll('label');
        for (const label of labels) {
            if (label.textContent.includes('Monto de Separación')) {
                // Buscar el input asociado
                const input = label.parentElement?.querySelector('input') || 
                             document.querySelector(`input[id="${label.getAttribute('for')}"]`);
                if (input && input.value) {
                    data.monto_separacion = input.value;
                    console.log('✓ Monto separación encontrado por label:', data.monto_separacion);
                    break;
                }
            }
        }
    }
    
    // Estrategia 4: Buscar en la estructura específica de Filament
    if (!data.monto_separacion) {
        // Buscar elementos con clases específicas de Filament
        const filamentInputs = document.querySelectorAll('.fi-input, [data-field-wrapper]');
        for (const wrapper of filamentInputs) {
            const label = wrapper.querySelector('label');
            const input = wrapper.querySelector('input');
            
            if (label && input && label.textContent.includes('Monto de Separación')) {
                data.monto_separacion = input.value;
                console.log('✓ Monto separación encontrado en Filament wrapper:', data.monto_separacion);
                break;
            }
        }
    }
    
    // Estrategia 5: Buscar en tablas (para casos donde el monto esté en una tabla)
    if (!data.monto_separacion) {
        const tables = document.querySelectorAll('table');
        for (const table of tables) {
            const rows = table.querySelectorAll('tr');
            for (const row of rows) {
                const cells = row.querySelectorAll('td, th');
                for (let i = 0; i < cells.length; i++) {
                    const cell = cells[i];
                    if (cell.textContent.includes('Monto de Separación') && cells[i + 1]) {
                        const valueCell = cells[i + 1];
                        const value = valueCell.textContent.trim().replace(/[^\d.]/g, '');
                        if (value && !isNaN(parseFloat(value))) {
                            data.monto_separacion = value;
                            console.log('✓ Monto separación encontrado en tabla:', data.monto_separacion);
                            break;
                        }
                    }
                }
                if (data.monto_separacion) break;
            }
            if (data.monto_separacion) break;
        }
    }
    
    console.log('📋 Datos extraídos del DOM:', data);
    return data;
}

// Función para actualizar el modal con datos de la proforma
function updateModalWithProformaData(data) {
    console.log('🔄 Actualizando modal con datos de proforma:', data);
    
    // Actualizar monto de separación
    const montoSeparacionElement = document.getElementById('monto-separacion-info');
    if (montoSeparacionElement && data.monto_separacion) {
        montoSeparacionElement.textContent = `(Monto separación: S/ ${parseFloat(data.monto_separacion).toFixed(2)})`;
        console.log('✅ Monto separación actualizado:', data.monto_separacion);
        
        // También establecer como valor por defecto en el campo monto si está vacío
        const montoInput = document.getElementById('monto');
        if (montoInput && (!montoInput.value || montoInput.value === '0' || montoInput.value === '0.00')) {
            montoInput.value = parseFloat(data.monto_separacion).toFixed(2);
            console.log('✅ Monto por defecto establecido:', data.monto_separacion);
        }
        
        // Guardar el monto para validaciones futuras
        window.montoSeparacionActual = parseFloat(data.monto_separacion);
    } else {
        console.warn('⚠️ No se pudo actualizar monto separación - elemento no encontrado o dato no disponible');
    }
}

</script>

<style>
/* Estilos adicionales para el modal */
#pago-separacion-modal {
    backdrop-filter: blur(4px);
}

#pago-separacion-modal .max-h-96 {
    max-height: 24rem;
}

#pago-separacion-modal .sticky {
    position: sticky;
}

#pago-separacion-modal .top-0 {
    top: 0;
}

/* Estilos para la tabla */
#pago-separacion-modal table {
    font-size: 0.875rem;
}

#pago-separacion-modal tbody tr:hover {
    background-color: #f9fafb;
}

/* Estilos para el formulario */
#pago-separacion-modal input:focus,
#pago-separacion-modal select:focus,
#pago-separacion-modal textarea:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Estilos para botones */
#pago-separacion-modal button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>