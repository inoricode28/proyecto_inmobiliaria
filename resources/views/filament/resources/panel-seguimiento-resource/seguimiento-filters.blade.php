<x-filament::card>
    {{ $this->form }}

    <div class="mt-4 text-right">
        <x-filament::button wire:click="submitFilters" color="primary">
            Buscar
        </x-filament::button>
    </div>
</x-filament::card>
