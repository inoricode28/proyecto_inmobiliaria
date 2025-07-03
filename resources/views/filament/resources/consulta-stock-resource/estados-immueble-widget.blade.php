<div class="flex items-center bg-white rounded-lg shadow px-4 py-2 border">
    <span class="font-bold text-gray-700 mr-4 whitespace-nowrap text-sm">
        Estados 
    </span>
    
    <div class="flex gap-2 overflow-x-auto  py-1  w-full">
        @foreach($this->getEstadosData() as $data)
            <button 
                type="button"
                wire:click="$dispatch('setEstadoFilter', { estado: '{{ $data['nombre'] }}' })"
                class="flex flex-col min-w-[150px] group transition-all hover:scale-80"
                title="{{ $data['descripcion'] }}"
            >
                <div class="h-2 w-full {{ $data['color'] }} rounded-t"></div>
                <div class="bg-gray-50 px-1 py-1 rounded-b text-center border border-gray-100">
                    <div class="text-xs font-medium text-gray-700 truncate">
                        {{ Str::limit($data['nombre'], 50) }}
                    </div>
                    <div class="text-sm font-bold text-gray-900">
                        {{ $data['count'] }}
                    </div>
                </div>
            </button>
        @endforeach
    </div>
</div>