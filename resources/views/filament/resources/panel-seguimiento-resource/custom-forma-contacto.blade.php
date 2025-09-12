@php
$formasPermitidas = [
9 => ['nombre' => 'WhatsApp', 'svg' => 'chat', 'color' => 'bg-lime-100', 'hover' => 'hover:bg-lime-200'],
8 => ['nombre' => 'Teléfono', 'svg' => 'phone', 'color' => 'bg-green-100', 'hover' => 'hover:bg-green-200'],
1 => ['nombre' => 'Correo', 'svg' => 'mail', 'color' => 'bg-blue-100', 'hover' => 'hover:bg-blue-200'],
// 2 => ['nombre' => 'Otros', 'svg' => 'globe', 'color' => 'bg-indigo-100', 'hover' => 'hover:bg-indigo-200'],
];

$selectedId = $getState();
@endphp

<input type="hidden" name="{{ $getStatePath() }}" wire:model="{{ $getStatePath() }}" />

<div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
    @foreach($formasPermitidas as $id => $config)
    @php
    $selected = $selectedId == $id;
    @endphp

    <button type="button" wire:click="$set('{{ $getStatePath() }}', {{ $id }})"
        class="flex flex-col items-center justify-center p-3 rounded-md transition-all h-24 w-full
                {{ $selected ? 'ring-2 ring-offset-2 ring-blue-500 bg-blue-500 text-white' : $config['color'] . ' text-gray-700 ' . $config['hover'] }}" title="{{ $config['nombre'] }}">
        @switch($config['svg'])
        @case('chat')
        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" />
        </svg>
        @break
        @case('phone')
        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path d="M3 5a2 2 0 012-2h2l2 5-3 3a15 15 0 006 6l3-3 5 2v2a2 2 0 01-2 2h-1c-8.28 0-15-6.72-15-15V5z" />
        </svg>
        @break
        @case('mail')
        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path d="M4 4h16v16H4z" stroke="none" />
            <path d="M4 4l8 8 8-8" />
        </svg>
        @break
        @case('globe')
        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path d="M12 2a10 10 0 100 20 10 10 0 000-20z" />
            <path d="M2 12h20M12 2a15 15 0 010 20" />
        </svg>
        @break
        @endswitch

        <span class="mt-2 text-sm font-medium">{{ $config['nombre'] }}</span>
    </button>
    @endforeach
</div>

@error($getStatePath())
<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
@enderror