<div>
    {{-- Usamos el componente modal de Filament --}}
    <x-filament::modal
        id="realizar-accion-modal"
        width="4xl"
        wire:model.defer="show"
        :close-button="true"
        :footer-actions="[]"
    >
        <x-slot name="header">
            Realizar Acción - {{ $nombre }} - {{ $telefono }}
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Izquierda: datos y formulario acción actual --}}
            <div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text" class="w-full mt-1 p-2 border rounded" value="{{ $nombre }}" disabled>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                    <input type="text" class="w-full mt-1 p-2 border rounded" value="{{ $telefono }}" disabled>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Proyecto</label>
                    <input type="text" class="w-full mt-1 p-2 border rounded" value="{{ $proyecto }}" disabled>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Forma de contacto</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($formaContactos as $fc)
                            <label class="inline-flex items-center mr-3">
                                <input type="radio" wire:model="forma_contacto_id" value="{{ $fc->id }}" class="mr-2">
                                <span>{{ $fc->nombre }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('forma_contacto_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha acción</label>
                        <input type="date" wire:model="fecha_realizar" class="w-full mt-1 p-2 border rounded">
                        @error('fecha_realizar') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hora</label>
                        <input type="time" wire:model="hora" class="w-full mt-1 p-2 border rounded">
                        @error('hora') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Resultado del contacto</label>
                    <label class="inline-flex items-center mr-3">
                        <input type="radio" wire:model="respuesta" value="efectiva" class="mr-2"> EFECTIVA
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" wire:model="respuesta" value="no_efectiva" class="mr-2"> NO EFECTIVA
                    </label>
                    @error('respuesta') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nivel de Interés</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($nivelesInteres as $ni)
                            <label class="inline-flex items-center mr-3">
                                <input type="radio" wire:model="nivel_interes_id" value="{{ $ni->id }}" class="mr-2">
                                <span>{{ $ni->nombre }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('nivel_interes_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Comentarios</label>
                    <textarea wire:model="comentario" rows="4" class="w-full mt-1 p-2 border rounded"></textarea>
                </div>
            </div>

            {{-- Derecha: próxima tarea y última acción --}}
            <div>
                <div class="mb-3">
                    <label class="inline-flex items-center">
                        <input type="checkbox" wire:model="crear_proxima_tarea" class="mr-2">
                        <span class="font-medium">Crear próxima tarea (opcional)</span>
                    </label>
                </div>

                <div class="{{ $crear_proxima_tarea ? '' : 'hidden' }}">
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700">Asignar a</label>
                        <select wire:model="proxima_usuario_asignado_id" class="w-full mt-1 p-2 border rounded">
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Forma de contacto (próxima)</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($formaContactos as $fc)
                                <label class="inline-flex items-center mr-3">
                                    <input type="radio" wire:model="proxima_forma_contacto_id" value="{{ $fc->id }}" class="mr-2">
                                    <span>{{ $fc->nombre }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fecha próxima</label>
                            <input type="date" wire:model="proxima_fecha" class="w-full mt-1 p-2 border rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Hora próxima</label>
                            <input type="time" wire:model="proxima_hora" class="w-full mt-1 p-2 border rounded">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700">Nota próxima</label>
                        <textarea wire:model="proxima_comentario" rows="3" class="w-full mt-1 p-2 border rounded"></textarea>
                    </div>
                </div>

                {{-- Última acción --}}
                <div class="mt-4 p-3 bg-gray-50 border rounded">
                    <div class="font-semibold mb-2">Última Acción</div>
                    @php
                        $ultima = \App\Models\Tarea::where('prospecto_id', $prospectoId)->latest('created_at')->first();
                    @endphp

                    @if ($ultima)
                        <div class="text-sm">
                            <div><strong>{{ strtoupper(optional($ultima->formaContacto)->nombre ?? 'SIN CONTACTO') }}</strong></div>
                            <div class="text-xs text-gray-600">Fecha: {{ optional($ultima->fecha_realizar)?->format('d/m/Y') }} {{ $ultima->hora }}</div>
                            <div class="mt-2"><strong>Nivel:</strong> {{ optional($ultima->nivelInteres)->nombre ?? '-' }} - <strong>Responsable:</strong> {{ optional($ultima->usuarioAsignado)->name ?? '-' }}</div>
                            <div class="mt-2"><strong>Nota:</strong> {{ $ultima->nota ?? 'Sin nota' }}</div>
                        </div>
                    @else
                        <div class="text-sm text-gray-600">Sin acciones anteriores.</div>
                    @endif
                </div>
            </div>
        </div>

        <x-slot name="footer">
            <div class="flex justify-end gap-2">
                <x-filament::button color="secondary" wire:click="$set('show', false)">Cancelar</x-filament::button>
                <x-filament::button color="primary" wire:click="save">Guardar</x-filament::button>
            </div>
        </x-slot>
    </x-filament::modal>
</div>
