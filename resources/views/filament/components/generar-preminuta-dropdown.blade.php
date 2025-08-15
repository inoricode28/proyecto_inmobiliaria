<div class="relative" x-data="{ open: false }">
    <button 
        type="button"
        @click="open = !open"
        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded inline-flex items-center transition-colors duration-200"
    >
        <span>Generar Pre-Minuta</span>
        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    
    <div 
        x-show="open" 
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200"
    >
        <div class="py-1">
            <button 
               wire:click="generatePreMinutaWord" 
               @click="open = false"
               class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                Pre-Minuta WORD
            </button>
            <button 
               wire:click="generatePreMinutaPDF" 
               @click="open = false"
               class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                Pre-Minuta PDF
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:load', function () {
        Livewire.on('download-file', function (data) {
            const link = document.createElement('a');
            link.href = data.url;
            link.download = data.filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    });
</script>