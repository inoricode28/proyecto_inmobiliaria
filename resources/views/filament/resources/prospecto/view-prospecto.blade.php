{{-- resources/views/filament/resources/prospecto/view-prospecto.blade.php --}}
<x-filament::page>
    <div class="space-y-6">
        {{-- Encabezado con info principal del prospecto --}}
        <div class="p-4 bg-white shadow rounded-lg">
            <h2 class="text-xl font-bold text-gray-800">
                {{ $record->nombre ?? 'Sin nombre' }}
            </h2>
            <p class="text-gray-600">
                Email: {{ $record->email ?? '-' }}
            </p>
            <p class="text-gray-600">
                Teléfono: {{ $record->telefono ?? '-' }}
            </p>
        </div>

        {{-- Aquí luego vamos a incluir botones y modales --}}
        {{-- @include('filament.resources.prospecto.partials.botones') --}}
        {{-- @include('filament.resources.prospecto.partials.modal-realizar-tarea') --}}
    </div>
</x-filament::page>
