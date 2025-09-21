<x-filament::card>
    {{ $this->form }}

    <div class="mt-4 flex justify-between items-center">
        <div class="flex gap-2">
            <!-- Botón Excel -->
            <a href="#" 
               onclick="exportToExcel()"
               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                Excel
            </a>
            
            <!-- Botón PDF -->
            <a href="#" 
               onclick="exportToPdf()"
               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                PDF
            </a>
        </div>
        
        <x-filament::button
            wire:click="submitFilters"
            color="primary"
            wire:loading.attr="disabled"
            wire:target="submitFilters">
            <span wire:loading.remove wire:target="submitFilters">Buscar</span>
            <span wire:loading wire:target="submitFilters">Procesando...</span>
        </x-filament::button>
    </div>
</x-filament::card>

<script>
function getFilterParams() {
    try {
        // Verificar que Livewire esté disponible
        if (typeof window.Livewire === 'undefined' || !window.Livewire) {
            console.error('Livewire no está disponible');
            return '';
        }
        
        // Obtener el componente Livewire actual usando Alpine.js
        const livewireComponent = this.$wire || window.Livewire.first();
        if (!livewireComponent) {
            console.error('No se pudo encontrar el componente Livewire');
            return '';
        }
        
        // Obtener los datos del formulario directamente desde el componente
        const formData = component.get('data');
        if (!formData) {
            console.error('No se pudieron obtener los datos del formulario');
            return '';
        }
        
        const params = new URLSearchParams();
        
        // Agregar todos los filtros como parámetros
        if (formData.proyecto) params.append('proyecto', formData.proyecto);
        if (formData.usuario_id) params.append('usuario_id', formData.usuario_id);
        if (formData.comoSeEntero) params.append('comoSeEntero', formData.comoSeEntero);
        if (formData.tipo_gestion_id) params.append('tipo_gestion_id', formData.tipo_gestion_id);
        if (formData.fechaInicio) params.append('fechaInicio', formData.fechaInicio);
        if (formData.fechaFin) params.append('fechaFin', formData.fechaFin);
        if (formData.NivelInteres) params.append('NivelInteres', formData.NivelInteres);
        if (formData.rangoAcciones) params.append('rangoAcciones', formData.rangoAcciones);
        if (formData.vencimiento) params.append('vencimiento', formData.vencimiento);
        
        // Obtener información de paginación de la tabla
        // Intentar diferentes selectores para encontrar el texto de paginación
        let paginationElement = document.querySelector('.fi-ta-pagination-records-text') ||
                               document.querySelector('.filament-tables-pagination-records-text') ||
                               document.querySelector('[class*="pagination-records"]') ||
                               document.querySelector('[class*="records-text"]') ||
                               document.querySelector('.fi-ta-pagination-summary') ||
                               document.querySelector('[data-testid="pagination-summary"]') ||
                               document.querySelector('.fi-pagination-summary');
        
        console.log('Elemento de paginación encontrado:', paginationElement);
        
        if (paginationElement) {
            const text = paginationElement.textContent || paginationElement.innerText;
            console.log('Texto de paginación:', text);
            
            // Intentar diferentes patrones de texto en español e inglés
            let match = text.match(/(\d+)\s+a\s+(\d+)/i) ||  // "1 a 10"
                       text.match(/(\d+)\s+to\s+(\d+)/i) ||  // "1 to 10"
                       text.match(/(\d+)\s*-\s*(\d+)/i) ||   // "1-10"
                       text.match(/Mostrando\s+(\d+)\s+a\s+(\d+)/i) || // "Mostrando 1 a 10"
                       text.match(/Showing\s+(\d+)\s+to\s+(\d+)/i);    // "Showing 1 to 10"
            
            console.log('Match encontrado:', match);
            
            if (match) {
                const start = parseInt(match[1]);
                const end = parseInt(match[2]);
                const perPage = end - start + 1;
                const currentPage = Math.ceil(start / perPage);
                
                console.log('Parámetros calculados:', { start, end, perPage, currentPage });
                
                params.append('page', currentPage);
                params.append('per_page', perPage);
            } else {
                // Si no encontramos el patrón, usar valores por defecto
                console.log('No se pudo extraer paginación, usando valores por defecto');
                params.append('page', 1);
                params.append('per_page', 10);
            }
        } else {
            console.log('No se encontró elemento de paginación, usando valores por defecto');
            params.append('page', 1);
            params.append('per_page', 10);
        }
        
        return params.toString();
    } catch (error) {
        console.error('Error obteniendo parámetros de filtro:', error);
        // En caso de error, devolver parámetros básicos de paginación
        const fallbackParams = new URLSearchParams();
        fallbackParams.append('page', 1);
        fallbackParams.append('per_page', 10);
        return fallbackParams.toString();
    }
}

function exportToExcel() {
    const params = getFilterParams();
    const url = '{{ route("seguimientos.export.excel") }}' + (params ? '?' + params : '');
    window.open(url, '_blank');
}

function exportToPdf() {
    const params = getFilterParams();
    const pdfUrl = '{{ route("seguimientos.export.pdf") }}' + (params ? '?' + params : '');
    window.open(pdfUrl, '_blank');
}
</script>
