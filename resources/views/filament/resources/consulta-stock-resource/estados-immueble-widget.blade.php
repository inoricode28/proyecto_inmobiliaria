<div class="bg-white rounded-lg shadow border w-full">
    <div class="flex items-center w-full px-2 py-2">
        <span class="font-bold text-gray-700 mr-3 whitespace-nowrap text-sm w-[70px] flex-shrink-0">
            Estados
        </span>

        <div class="grid grid-cols-9 gap-1 flex-1 min-w-0">
            @foreach($this->getEstadosData() as $data)
                <div class="min-w-0">
                    <button
                        type="button"
                        wire:click="$dispatch('setEstadoFilter', { estado: '{{ $data['nombre'] }}' })"
                        class="flex flex-col w-full h-full"
                        title="{{ $data['descripcion'] }}"
                    >
                        <!-- Barra de color superior -->
                        <div class="h-1.5 w-full" style="background-color: {{ $data['color'] }}"></div>

                        <!-- Contenido del botón -->
                        <div class="bg-gray-50 p-1 border border-gray-200 flex flex-col items-center min-w-0">
                            <div class="text-[10px] font-medium text-gray-700 text-center break-all leading-tight">
                                {{ $data['nombre'] }}
                            </div>
                            <div class="text-xs font-bold text-gray-900 mt-0.5">
                                {{ $data['count'] }}
                            </div>
                        </div>
                    </button>
                </div>
            @endforeach
        </div>
    </div>
</div>
