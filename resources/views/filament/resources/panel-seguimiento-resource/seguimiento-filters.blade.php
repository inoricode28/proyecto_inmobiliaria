<x-filament::card>
    {{ $this->form }}

    <div class="mt-4 flex justify-between items-center">
        <div class="flex gap-2">
            <!-- Botón Excel -->
            <a href="#" onclick="exportToExcel()"
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
                Excel
            </a>

            <!-- Botón PDF -->
            <a href="#" onclick="exportToPdf()"
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
                PDF
            </a>
        </div>

        <x-filament::button wire:click="submitFilters" color="primary" wire:loading.attr="disabled"
            wire:target="submitFilters">
            <span wire:loading.remove wire:target="submitFilters">Buscar</span>
            <span wire:loading wire:target="submitFilters">Procesando...</span>
        </x-filament::button>
    </div>
</x-filament::card>

<script>
function exportToExcel() {
    // Exportación simple sin parámetros complejos
    const url = '{{ route("seguimientos.export.excel") }}';
    console.log('Exportando Excel:', url);
    window.open(url, '_blank');
}

function exportToPdf() {
    // Exportación simple sin parámetros complejos
    const url = '{{ route("seguimientos.export.pdf") }}';
    console.log('Exportando PDF:', url);
    window.open(url, '_blank');
}
</script>
