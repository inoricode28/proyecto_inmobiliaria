<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <x-filament::button
        color="success"
        icon="heroicon-o-upload"
        wire:click="$emit('openImportModal')"
    >
        Importar Prospectos
    </x-filament::button>
</x-dynamic-component>

