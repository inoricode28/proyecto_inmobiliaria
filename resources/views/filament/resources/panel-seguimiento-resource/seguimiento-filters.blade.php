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
// Obtiene los filtros actuales directamente desde los inputs del formulario (estado Livewire)
function getCurrentFilters() {
    const getVal = (name) => document.querySelector(`[name="data.${name}"]`)?.value;
    const toInt = (v) => {
        if (v === undefined || v === null || v === '' ) return null;
        const n = Number(v);
        return Number.isNaN(n) ? v : n;
    };

    return {
        proyecto: toInt(getVal('proyecto')),
        usuario_id: toInt(getVal('usuario_id')) ?? 0,
        comoSeEntero: toInt(getVal('comoSeEntero')) ?? 0,
        tipo_gestion_id: toInt(getVal('tipo_gestion_id')),
        fechaInicio: getVal('fechaInicio') || null,
        fechaFin: getVal('fechaFin') || null,
        NivelInteres: toInt(getVal('NivelInteres')) ?? 0,
        rangoAcciones: getVal('rangoAcciones') || 0,
        vencimiento: getVal('vencimiento') || 0,
    };
}

function buildQueryParams(filters) {
    const params = new URLSearchParams();
    const map = {
        proyecto: filters.proyecto,
        usuario_id: filters.usuario_id,
        comoSeEntero: filters.comoSeEntero,
        tipo_gestion_id: filters.tipo_gestion_id,
        fechaInicio: filters.fechaInicio,
        fechaFin: filters.fechaFin,
        NivelInteres: filters.NivelInteres,
        rangoAcciones: filters.rangoAcciones,
        vencimiento: filters.vencimiento,
    };
    Object.entries(map).forEach(([key, value]) => {
        if (value !== null && value !== undefined && value !== '' && value !== 0) {
            params.append(key, value);
        }
    });
    return params.toString();
}

function exportToExcel() {
    const baseUrl = '{{ route("seguimientos.export.excel") }}';
    const filters = getCurrentFilters();
    const qs = buildQueryParams(filters);
    const url = qs ? `${baseUrl}?${qs}` : baseUrl;
    window.open(url, '_blank');
}

function exportToPdf() {
    const baseUrl = '{{ route("seguimientos.export.pdf") }}';
    const filters = getCurrentFilters();
    const qs = buildQueryParams(filters);
    const url = qs ? `${baseUrl}?${qs}` : baseUrl;
    window.open(url, '_blank');
}
</script>
