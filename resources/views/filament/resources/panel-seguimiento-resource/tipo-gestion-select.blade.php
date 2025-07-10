@php
    $tiposGestion = \App\Models\TipoGestion::all();
    $currentValue = $getState();

    // Obtener conteos por tipo de gestión
    $conteos = [];
    foreach ($tiposGestion as $tipo) {
        $conteos[$tipo->id] = \App\Models\Prospecto::where('tipo_gestion_id', $tipo->id)->count();
    }

    // Configuración de colores para cada tipo
    $colores = [
        1 => ['bg' => 'bg-gray-100', 'selected' => 'bg-gray-600', 'text' => 'text-gray-800'],
        2 => ['bg' => 'bg-blue-100', 'selected' => 'bg-blue-600', 'text' => 'text-blue-800'],
        3 => ['bg' => 'bg-green-100', 'selected' => 'bg-green-600', 'text' => 'text-green-800'],
        4 => ['bg' => 'bg-yellow-100', 'selected' => 'bg-yellow-600', 'text' => 'text-yellow-800'],
        5 => ['bg' => 'bg-red-100', 'selected' => 'bg-red-600', 'text' => 'text-red-800']
    ];
@endphp

<input type="hidden" name="{{ $getStatePath() }}" wire:model="{{ $getStatePath() }}" />

<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-6 gap-3 w-full">
    @foreach($tiposGestion as $tipo)
        @php
            $colorConfig = $colores[$tipo->id] ?? $colores[1];
            $isSelected = $currentValue == $tipo->id;
        @endphp

        <button
            type="button"
            wire:click="$set('{{ $getStatePath() }}', {{ $tipo->id }})"
            class="flex flex-col items-center justify-center p-3 rounded-lg transition-all h-28
                {{ $isSelected
                    ? $colorConfig['selected'] . ' text-white'
                    : $colorConfig['bg'] . ' ' . $colorConfig['text'] . ' hover:shadow-md' }}"
            title="{{ $tipo->descripcion ?? $tipo->nombre }}"
        >
            <!-- Icono según tipo de gestión -->
            @switch($tipo->id)
                @case(1) <!-- NO GESTIONADO -->
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    @break
                @case(2) <!-- POR CONTACTAR -->
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    @break
                @case(3) <!-- CONTACTADOS -->
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    @break
                @case(4) <!-- VISITAS -->
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    @break
                @case(5) <!-- SEPARACIONES -->
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    @break
                @default
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
            @endswitch

            <span class="mt-1 text-xs text-center">{{ $tipo->nombre }}</span>
            <span class="mt-1 text-lg font-bold">{{ $conteos[$tipo->id] }}</span>
        </button>
    @endforeach
</div>

@error($getStatePath())
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
@enderror
