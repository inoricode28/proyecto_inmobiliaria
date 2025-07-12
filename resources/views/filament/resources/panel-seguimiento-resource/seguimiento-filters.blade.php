<x-filament::card>
    {{ $this->form }}

    <div class="mt-4 text-right">
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
