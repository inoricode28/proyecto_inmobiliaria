{{-- Modal de Registro de Pago de Separación --}}
<div id="pago-separacion-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
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
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                            </label>
                            <input type="number" id="monto" name="monto" step="0.01" min="0" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="0.00">
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
                            <input type="number" id="tipo_cambio" name="tipo_cambio" step="0.0001" min="0" value="1.0000"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        {{-- Monto de Pago --}}
                        <div>
                            <label for="monto_pago" class="block text-sm font-medium text-gray-700 mb-2">
                                Monto de Pago <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="monto_pago" name="monto_pago" step="0.01" min="0" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="0.00" readonly>
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
                </form>

                {{-- Tabla de Pagos Registrados --}}
                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Lista de Pagos Registrados</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Pago</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
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
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">
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
                <button type="button" id="agregar-pago-btn" onclick="agregarPago()"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                    Agregar Pago
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
            openPagoSeparacionModal();
        }
    });

    // Función para abrir el modal
    window.openPagoSeparacionModal = function() {
        const modal = document.getElementById('pago-separacion-modal');
        if (modal) {
            modal.classList.remove('hidden');
            
            // Cargar datos iniciales
            setTimeout(() => {
                loadInitialData();
                setDefaultDate();
                loadExistingPagos();
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
        const tipoCambio = parseFloat(document.getElementById('tipo_cambio').value) || 1;
        const montoPago = monto * tipoCambio;
        
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
        const separacionId = document.getElementById('separacion_id').value;
        if (!separacionId) return;

        fetch(`/api/pagos-separacion/${separacionId}`)
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('pagos-table-body');
                const noDataRow = document.getElementById('no-pagos-row');
                
                if (data.success && data.data && data.data.length > 0) {
                    // Ocultar fila de "no hay datos"
                    noDataRow.style.display = 'none';
                    
                    // Limpiar filas existentes (excepto la de "no hay datos")
                    const existingRows = tbody.querySelectorAll('tr:not(#no-pagos-row)');
                    existingRows.forEach(row => row.remove());
                    
                    // Agregar filas de pagos
                    data.data.forEach(pago => {
                        const row = createPagoRow(pago);
                        tbody.appendChild(row);
                    });
                } else {
                    // Mostrar fila de "no hay datos"
                    noDataRow.style.display = 'table-row';
                    
                    // Limpiar filas existentes
                    const existingRows = tbody.querySelectorAll('tr:not(#no-pagos-row)');
                    existingRows.forEach(row => row.remove());
                }
            })
            .catch(error => {
                console.error('Error al cargar pagos:', error);
            });
    }

    // Función para crear fila de pago
    function createPagoRow(pago) {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-4 py-3 text-sm text-gray-900">${formatDate(pago.fecha_pago)}</td>
            <td class="px-4 py-3 text-sm text-gray-900">${formatCurrency(pago.monto)}</td>
            <td class="px-4 py-3 text-sm text-gray-900">${pago.moneda?.nombre || 'N/A'}</td>
            <td class="px-4 py-3 text-sm text-gray-900">${pago.tipo_cambio}</td>
            <td class="px-4 py-3 text-sm text-gray-900">${formatCurrency(pago.monto_pago)}</td>
            <td class="px-4 py-3 text-sm text-gray-900">${pago.medio_pago?.nombre || 'N/A'}</td>
            <td class="px-4 py-3 text-sm text-gray-900">${pago.numero_operacion || 'N/A'}</td>
            <td class="px-4 py-3 text-sm text-gray-900">
                <button onclick="eliminarPago(${pago.id})" 
                    class="text-red-600 hover:text-red-800 text-sm">
                    Eliminar
                </button>
            </td>
        `;
        return row;
    }

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
        document.getElementById('pago-separacion-form').reset();
        document.getElementById('monto_pago').value = '';
        setDefaultDate();
    }

    // Función para obtener ID de separación actual
    function getCurrentSeparacionId() {
        // Reutilizar la lógica de los otros modales
        let separacionId = null;
        
        // Estrategia 1: Buscar en URL
        const pathParts = window.location.pathname.split('/');
        const separacionIndex = pathParts.indexOf('separacions');
        if (separacionIndex !== -1 && pathParts[separacionIndex + 1]) {
            separacionId = pathParts[separacionIndex + 1];
            console.log('🔍 Separación ID encontrado en ruta:', separacionId);
            return separacionId;
        }

        // Estrategia 2: Buscar en elementos del DOM
        const separacionElement = document.querySelector('[data-separacion-id]');
        if (separacionElement) {
            separacionId = separacionElement.getAttribute('data-separacion-id');
            console.log('🔍 Separación ID encontrado en DOM:', separacionId);
            return separacionId;
        }

        // Estrategia 3: Buscar en variables globales
        if (typeof window.separacionId !== 'undefined') {
            console.log('🔍 Separación ID encontrado en variable global:', window.separacionId);
            return window.separacionId;
        }

        console.log('❌ No se pudo encontrar el ID de la separación');
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