<div class="flex items-center gap-3 my-2">
    {{-- Proformar --}}
    <a
        href="{{ $proformaUrl }}"
        target="_blank"
        class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none"
    >
        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16h8M8 12h8m-8-4h8M4 6h16v12H4z"/>
        </svg>
        Proformar
    </a>

    {{-- Realizar Tarea (abre modal) --}}
    <button
        type="button"
        wire:click="openRealizarTareaModal"
        class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none"
    >
        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M9 8h6M5 6h14v12H5z"/>
        </svg>
        Realizar Tarea
    </button>

    {{-- Reasignar (deshabilitado por ahora) --}}
    <button
        type="button"
        disabled
        class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium text-white bg-gray-400 cursor-not-allowed opacity-70"
    >
        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V9H2v11h5M7 9V4h10v5"/>
        </svg>
        Reasignar
    </button>
</div>
