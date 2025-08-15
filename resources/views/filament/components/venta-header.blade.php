<div class="bg-white border border-gray-200 rounded-lg p-4 mb-6 shadow-sm" x-data="ventaHeader()" x-init="init()">

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Fecha Entrega Inicial -->
        <div class="flex flex-col">
            <label class="text-sm font-medium text-gray-700 mb-2">Fecha Entrega Inicial</label>
            <input type="date" x-model="fechaEntregaInicial"
                @change="updateField('fecha_entrega_inicial', $event.target.value)"
                class="bg-gray-100 border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <!-- Fecha Venta -->
        <div class="flex flex-col">
            <label class="text-sm font-medium text-gray-700 mb-2">Fecha Venta</label>
            <input type="date" x-model="fechaVenta" @change="updateField('fecha_venta', $event.target.value)"
                class="bg-gray-100 border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <!-- Fecha Preminuta -->
        <div class="flex flex-col relative">
            <label class="text-sm font-medium text-gray-700 mb-2">Fecha Preminuta</label>
            <input type="date" x-model="fechaPreminuta" @change="updateField('fecha_preminuta', $event.target.value)"
                class="bg-gray-100 border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <div class="relative mt-2">
                <button @click="showDropdown = !showDropdown"
                    class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded transition-colors duration-200 flex items-center gap-1">
                    Generar Pre-Minuta
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="showDropdown" @click.away="showDropdown = false" x-transition
                    class="absolute top-full left-0 mt-1 bg-white border border-gray-200 rounded-md shadow-lg z-10 min-w-max">
                    <button @click="generarPreminuta('pdf'); showDropdown = false"
                        class="block w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-gray-100">
                        Pre-minuta PDF
                    </button>
                    <button @click="generarPreminuta('word'); showDropdown = false"
                        class="block w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-gray-100">
                        Pre-minuta Word
                    </button>
                </div>
            </div>
        </div>

        <!-- Fecha Minuta -->
        <div class="flex flex-col">
            <label class="text-sm font-medium text-gray-700 mb-2">Fecha Minuta</label>
            <input type="date" x-model="fechaMinuta" @change="updateField('fecha_minuta', $event.target.value)"
                class="bg-gray-100 border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>

    <!-- Botón de Acción -->
    <div class="mt-4 flex justify-end">
        <button @click="crearVenta()"
            class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-500 hover:bg-blue-600 text-white transition-colors duration-200 cursor-pointer">
            VENDIDO
        </button>
    </div>

    <!-- Información adicional de separación -->
    <div class="mt-4 pt-4 border-t border-gray-200" x-show="separacionInfo">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
            <div x-show="clienteNombre">
                <span class="font-medium">Cliente:</span> <span x-text="clienteNombre"></span>
            </div>
            <div x-show="proyectoNombre">
                <span class="font-medium">Proyecto:</span> <span x-text="proyectoNombre"></span>
            </div>
            <div x-show="inmuebleNumero">
                <span class="font-medium">Inmueble:</span> <span x-text="inmuebleNumero"></span>
            </div>
        </div>
    </div>
</div>

<script>
function ventaHeader() {
    return {
        fechaEntregaInicial: '',
        fechaVenta: '',
        fechaPreminuta: '',
        fechaMinuta: '',
        estado: 'VENDIDO',
        separacionInfo: false,
        clienteNombre: '',
        proyectoNombre: '',
        inmuebleNumero: '',
        showDropdown: false,

        init() {
            // Escuchar cambios en los campos de Filament
            this.listenToFilamentChanges();
            // Sincronizar fechas iniciales desde los campos de Filament con delay
            setTimeout(() => {
                this.syncInitialDates();
            }, 500);
        },

        syncInitialDates() {
            // Obtener valores iniciales de los campos de Filament
            const fechaEntregaField = document.querySelector('[name="fecha_entrega_inicial"]');
            const fechaVentaField = document.querySelector('[name="fecha_venta"]');
            const fechaPreminutaField = document.querySelector('[name="fecha_preminuta"]');
            const fechaMinutaField = document.querySelector('[name="fecha_minuta"]');

            if (fechaEntregaField && fechaEntregaField.value) {
                this.fechaEntregaInicial = fechaEntregaField.value;
            }
            if (fechaVentaField && fechaVentaField.value) {
                this.fechaVenta = fechaVentaField.value;
            }
            if (fechaPreminutaField && fechaPreminutaField.value) {
                this.fechaPreminuta = fechaPreminutaField.value;
            }
            if (fechaMinutaField && fechaMinutaField.value) {
                this.fechaMinuta = fechaMinutaField.value;
            }
        },

        updateField(fieldName, value) {
            // Actualizar el campo correspondiente en Filament
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.value = value;
                field.dispatchEvent(new Event('change', {
                    bubbles: true
                }));
                field.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
            }

            // También buscar campos ocultos de Filament
            const hiddenField = document.querySelector(`input[type="hidden"][name="${fieldName}"]`);
            if (hiddenField) {
                hiddenField.value = value;
                hiddenField.dispatchEvent(new Event('change', {
                    bubbles: true
                }));
            }

            // Disparar evento personalizado para notificar cambios
            window.dispatchEvent(new CustomEvent('ventaFieldUpdated', {
                detail: {
                    fieldName,
                    value
                }
            }));
        },

        generarPreminuta(type) {
            // Solo mostrar las opciones disponibles sin ejecutar ninguna acción
            console.log(`Opción seleccionada: Pre-minuta ${type.toUpperCase()}`);
            // Aquí se puede implementar la lógica para mostrar/descargar el documento
            // sin ejecutar ninguna acción de creación de venta
        },

        crearVenta() {
            // Implementar la lógica real para crear la venta
            alert('Creando venta...');
            // Aquí se debe implementar la llamada al backend para crear la venta
            // Ejemplo: this.$wire.call('crearVenta')
        },

        listenToFilamentChanges() {
            // Escuchar eventos personalizados de JavaScript
            window.addEventListener('updateVentaHeader', (event) => {
                const data = event.detail;
                this.clienteNombre = data.clienteNombre || '';
                this.proyectoNombre = data.proyectoNombre || '';
                this.inmuebleNumero = data.inmuebleNumero || '';
                this.separacionInfo = data.separacionInfo || false;
            });

            // Escuchar cambios en los campos de fecha de Filament
            const observeFieldChanges = () => {
                const fields = [{
                        name: 'fecha_entrega_inicial',
                        model: 'fechaEntregaInicial'
                    },
                    {
                        name: 'fecha_venta',
                        model: 'fechaVenta'
                    },
                    {
                        name: 'fecha_preminuta',
                        model: 'fechaPreminuta'
                    },
                    {
                        name: 'fecha_minuta',
                        model: 'fechaMinuta'
                    }
                ];

                fields.forEach(field => {
                    const element = document.querySelector(`[name="${field.name}"]`);
                    if (element) {
                        element.addEventListener('change', (e) => {
                            this[field.model] = e.target.value;
                        });
                    }
                });
            };

            // Ejecutar después de que Filament haya renderizado
            setTimeout(observeFieldChanges, 100);
        },

        updateFromSeparacion() {
            // Esta función se llamará cuando se actualicen los datos de separación
            const separacionSelect = document.querySelector('[name="separacion_id"]');
            if (separacionSelect && separacionSelect.value) {
                this.separacionInfo = true;
            }
        }
    }
}
</script>
