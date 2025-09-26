@if(isset($error))
    <div class="p-6 text-center">
        <div class="text-red-600 mb-4">
            <i class="fas fa-exclamation-triangle text-4xl"></i>
        </div>
        <p class="text-gray-700">{{ $error }}</p>
    </div>
@else
    <div class="p-6">
        <!-- Información del Inmueble -->
        <div class="mb-6 p-4 bg-blue-50 rounded-lg">
            <h3 class="text-lg font-semibold text-blue-800 mb-3">Información del Inmueble</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <span class="font-medium text-gray-700">Proyecto:</span>
                    <div class="text-blue-600 font-semibold">{{ $proforma->proyecto->nombre ?? 'N/A' }}</div>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Inmueble:</span>
                    <div class="text-blue-600 font-semibold">{{ $proforma->departamento->num_departamento ?? 'N/A' }}</div>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Precio Venta:</span>
                    <div class="text-blue-600 font-semibold">S/ {{ number_format($proforma->precio_venta ?? 0, 2) }}</div>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Cuota Inicial:</span>
                    <div class="text-blue-600 font-semibold">S/ {{ number_format($proforma->monto_cuota_inicial ?? 0, 2) }}</div>
                </div>
            </div>
        </div>

        <!-- Formulario de Cronograma -->
        <div class="space-y-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio:</label>
                    <input type="date" id="fechaInicio" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ date('Y-m-d') }}" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Monto Total:</label>
                    <input type="number" id="montoTotal" class="w-full p-2 border border-gray-300 rounded-lg bg-gray-100" value="{{ $proforma->monto_cuota_inicial ?? 0 }}" step="0.01" readonly>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Número de Cuotas:</label>
                    <input type="number" id="numeroCuotas" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" min="1" max="12" value="3" required>
                </div>
            </div>

            <div class="flex justify-center">
                <button type="button" id="generarCuotas" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-calendar-plus mr-2"></i>Generar Cronograma
                </button>
            </div>
        </div>

        <!-- Tabla de Cuotas -->
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const generarCuotas = document.getElementById('generarCuotas');
            const cuotasSection = document.getElementById('cuotasSection');
            const cuotasTableBody = document.getElementById('cuotasTableBody');

            // Tipos de cuota disponibles
            const tiposCuota = ['Cuota Inicial', 'Ahorro Casa', 'AFP Titular', 'AFP Cónyuge'];

            // Generar cuotas
            generarCuotas.addEventListener('click', function() {
                const fechaInicio = document.getElementById('fechaInicio').value;
                const montoTotal = parseFloat(document.getElementById('montoTotal').value);
                const numeroCuotas = parseInt(document.getElementById('numeroCuotas').value);

                if (!fechaInicio || !numeroCuotas || numeroCuotas < 1) {
                    alert('Por favor, complete todos los campos correctamente.');
                    return;
                }

                // Limpiar tabla existente
                cuotasTableBody.innerHTML = '';

                // Calcular monto por cuota
                const montoPorCuota = montoTotal / numeroCuotas;

                // Generar cuotas
                for (let i = 0; i < numeroCuotas; i++) {
                    const fechaCuota = new Date(fechaInicio);
                    fechaCuota.setMonth(fechaCuota.getMonth() + i);

                    const row = document.createElement('tr');
                    row.className = 'hover:bg-gray-50';
                    row.innerHTML = `
                        <td class="border border-gray-300 px-4 py-2">
                            <input type="date" value="${fechaCuota.toISOString().split('T')[0]}" class="w-full p-1 border border-gray-300 rounded focus:ring-1 focus:ring-blue-500" required>
                        </td>
                        <td class="border border-gray-300 px-4 py-2">
                            <input type="number" value="${montoPorCuota.toFixed(2)}" step="0.01" class="w-full p-1 border border-gray-300 rounded focus:ring-1 focus:ring-blue-500" required>
                        </td>
                        <td class="border border-gray-300 px-4 py-2">
                            <select class="w-full p-1 border border-gray-300 rounded focus:ring-1 focus:ring-blue-500" required>
                                ${tiposCuota.map((tipo, index) => 
                                    `<option value="${tipo}" ${index === 0 ? 'selected' : ''}>${tipo}</option>`
                                ).join('')}
                            </select>
                        </td>
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            <button type="button" class="text-red-600 hover:text-red-800 eliminar-cuota p-1 rounded hover:bg-red-50" title="Eliminar cuota">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;

                    cuotasTableBody.appendChild(row);
                }

                // Mostrar sección de cuotas
                cuotasSection.classList.remove('hidden');

                // Agregar event listeners para eliminar cuotas
                document.querySelectorAll('.eliminar-cuota').forEach(btn => {
                    btn.addEventListener('click', function() {
                        if (confirm('¿Está seguro de eliminar esta cuota?')) {
                            this.closest('tr').remove();
                            
                            // Si no quedan cuotas, ocultar la sección
                            if (cuotasTableBody.children.length === 0) {
                                cuotasSection.classList.add('hidden');
                            }
                        }
                    });
                });
            });
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
    </style>
@endif