@php
    $niveles = \App\Models\NivelInteres::all();
    $currentValue = $getState();
    $error = $errors->first($getStatePath());
@endphp

<div class="space-y-2">
    <label class="block text-sm font-medium text-gray-700">
        Nivel de interés <span class="text-red-500">*</span>
    </label>
    <input type="hidden" name="nivel_interes_id" wire:model="{{ $getStatePath() }}" required>

    <div class="flex flex-wrap gap-2">
        @foreach($niveles as $nivel)
            <button
                type="button"
                wire:click="$set('{{ $getStatePath() }}', {{ $nivel->id }})"
                class="px-4 py-2 rounded-md text-sm font-medium transition-colors
                    {{ $currentValue == $nivel->id
                        ? 'bg-'.$nivel->color.'-500 text-white'
                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
            >
                {{ $nivel->nombre }}
            </button>
        @endforeach
    </div>

    @error($getStatePath())
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
