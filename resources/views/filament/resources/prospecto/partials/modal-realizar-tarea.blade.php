<x-filament::modal
    id="realizar-tarea-modal"
    width="4xl"
    :visible="$realizarTareaModal"
    :close-button="true"
    :footer-actions="[]"
    wire:model.defer="realizarTareaModal"
>
    <x-slot name="header">
        Realizar Tarea — {{ trim(($this->record->nombres ?? '') . ' ' . ($this->record->ape_paterno ?? '') . ' ' . ($this->record->ape_materno ?? '')) ?: ($this->record->razon_social ?? '') }}
        — {{ $this->record->celular }}
    </x-slot>

    <div class="space-y-4">
        {{-- IMPORTANTE: aquí se renderiza el formulario Filament del modal --}}
        {{ $this->realizarTareaForm }}

        <x-filament::button color="primary" wire:click="submitRealizarTarea">
            Guardar
        </x-filament::button>
    </div>
</x-filament::modal>
