{{-- Modal Realizar Acción --}}
<div>
    @if($showRealizarAccionModal)
    <div class="fixed inset-0 z-40 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            {{-- Background overlay --}}
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
                wire:click="cerrarModalRealizarTarea"></div>

            {{-- Modal panel --}}
            <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full z-50">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    {{-- Header --}}
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Realizar Tarea - {{ $this->nombreCompleto }} - {{ $this->telefonoCompleto }}
                        </h3>
                        <button type="button" wire:click="cerrarModalRealizarTarea"
                            class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Error messages --}}
                    @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
                        <ul class="text-sm text-red-600">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Success message --}}
                    @if (session()->has('message'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md">
                        <p class="text-sm text-green-600">{{ session('message') }}</p>
                    </div>
                    @endif

                    {{-- Form --}}
                    <form wire:submit.prevent="realizarTarea">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Columna izquierda --}}
                            <div class="bg-white border border-gray-200 rounded-lg p-6 space-y-4">
                                {{-- Información del prospecto --}}
                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nombre del Prospecto</label>
                                        <input type="text" class="w-full mt-1 p-2 border rounded bg-gray-50"
                                            value="{{ $this->nombreCompleto }}" disabled>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Teléfono/Celular</label>
                                        <input type="text" class="w-full mt-1 p-2 border rounded bg-gray-50"
                                            value="{{ $this->telefonoCompleto }}" disabled>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Proyecto de interés</label>
                                        <input type="text" class="w-full mt-1 p-2 border rounded bg-gray-50"
                                            value="{{ optional($this->prospecto->proyecto)->nombre ?? '' }}" disabled>
                                    </div>
                                </div>

                                {{-- Forma de contacto --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Forma de contacto</label>
                                    @php
                                        $formasPermitidas = [
                                            1 => ['nombre' => 'Correo', 'svg' => 'mail', 'color' => 'bg-blue-100', 'hover' => 'hover:bg-blue-200'],
                                            2 => ['nombre' => 'Facebook', 'svg' => 'globe', 'color' => 'bg-indigo-100', 'hover' => 'hover:bg-indigo-200'],
                                            8 => ['nombre' => 'Teléfono', 'svg' => 'phone', 'color' => 'bg-green-100', 'hover' => 'hover:bg-green-200'],
                                            9 => ['nombre' => 'WhatsApp', 'svg' => 'chat', 'color' => 'bg-lime-100', 'hover' => 'hover:bg-lime-200'],
                                        ];
                                    @endphp
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                        @foreach($formasPermitidas as $id => $config)
                                            @php
                                                $selected = $forma_contacto_id == $id;
                                            @endphp
                                            <button
                                                type="button"
                                                wire:click="$set('forma_contacto_id', {{ $id }})"
                                                class="flex flex-col items-center justify-center p-3 rounded-md transition-all h-24 w-full
                                                    {{ $selected ? 'ring-2 ring-offset-2 ring-blue-500 bg-blue-500 text-white' : $config['color'] . ' text-gray-700 ' . $config['hover'] }}"
                                                title="{{ $config['nombre'] }}">
                                                @switch($config['svg'])
                                                    @case('mail')
                                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                            <path d="M4 4h16v16H4z" stroke="none"/>
                                                            <path d="M4 4l8 8 8-8" />
                                                        </svg>
                                                        @break
                                                    @case('globe')
                                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                            <path d="M12 2a10 10 0 100 20 10 10 0 000-20z" />
                                                            <path d="M2 12h20M12 2a15 15 0 010 20" />
                                                        </svg>
                                                        @break
                                                    @case('phone')
                                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                            <path d="M3 5a2 2 0 012-2h2l2 5-3 3a15 15 0 006 6l3-3 5 2v2a2 2 0 01-2 2h-1c-8.28 0-15-6.72-15-15V5z" />
                                                        </svg>
                                                        @break
                                                    @case('chat')
                                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                            <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" />
                                                        </svg>
                                                        @break
                                                @endswitch
                                                <span class="text-xs font-medium mt-1">{{ $config['nombre'] }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Fecha y hora --}}
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Fecha acción</label>
                                        <input type="date" wire:model="fecha_realizar" class="w-full mt-1 p-2 border rounded" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Hora acción</label>
                                        <input type="time" wire:model="hora" class="w-full mt-1 p-2 border rounded" required>
                                    </div>
                                </div>

                                {{-- Resultado del contacto --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Resultado del contacto</label>
                                    <div class="flex space-x-4">
                                        <label class="flex items-center">
                                            <input type="radio" wire:model="respuesta" value="efectiva" class="mr-2" required>
                                            <span class="text-sm">EFECTIVA</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" wire:model="respuesta" value="no_efectiva" class="mr-2" required>
                                            <span class="text-sm">NO EFECTIVA</span>
                                        </label>
                                    </div>
                                </div>

                                {{-- Comentarios --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Comentarios adicionales</label>
                                    <textarea wire:model="comentario" class="w-full mt-1 p-2 border rounded" rows="3" 
                                        placeholder="Detalles de la conversación" maxlength="500"></textarea>
                                </div>

                                {{-- Nivel de interés --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nivel de Interés</label>
                                    <div class="flex flex-wrap gap-4">
                                        @foreach(\App\Models\NivelInteres::all() as $nivel)
                                            <label class="flex items-center">
                                                <input type="radio" wire:model="nivel_interes_id" value="{{ $nivel->id }}" class="mr-2" required>
                                                <span class="text-sm">{{ $nivel->nombre }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Última Acción --}}
                                @if($this->prospecto->tareas()->latest('created_at')->first())
                                    @php
                                        $ultimaTarea = $this->prospecto->tareas()->latest('created_at')->first();
                                    @endphp
                                    <hr class="my-4">
                                    <div class="text-base font-semibold text-gray-800 mb-2">Última Acción</div>
                                    <div class="space-y-2 text-sm text-gray-700 leading-snug">
                                        <div class="flex flex-wrap gap-4">
                                            <div><strong>Forma de contacto: </strong> {{ $ultimaTarea->formaContacto?->nombre }}</div>
                                            <div><strong>Fecha: </strong> {{ $ultimaTarea->fecha_realizar->format('d/m/Y') }}</div>
                                            <div><strong>Hora: </strong> {{ $ultimaTarea->hora }}</div>
                                        </div>
                                        <div><strong>Nivel de interés:</strong> {{ $ultimaTarea->nivelInteres?->nombre }}</div>
                                        <div><strong>Nota:</strong> {{ $ultimaTarea->nota }}</div>
                                    </div>
                                @else
                                    <hr class="my-4">
                                    <div class="text-sm text-gray-500 font-medium">Última Acción</div>
                                    <div class="text-sm text-gray-500">Sin acciones anteriores.</div>
                                @endif
                            </div>

                            {{-- Columna derecha --}}
                            <div class="bg-white border border-gray-200 rounded-lg p-6 space-y-4">
                                {{-- Crear próxima tarea --}}
                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="crear_proxima_tarea" class="mr-2" checked>
                                        <span class="text-sm font-medium">¿Crear próxima tarea?</span>
                                    </label>
                                </div>

                                @if($crear_proxima_tarea)
                                    {{-- Asignar a --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Asignar a</label>
                                        <select wire:model="proxima_usuario_asignado_id" class="w-full mt-1 p-2 border rounded" required>
                                            <option value="">Seleccionar usuario</option>
                                            @foreach(\App\Models\User::all() as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Forma de contacto próxima --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Forma de contacto</label>
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                            @foreach($formasPermitidas as $id => $config)
                                                @php
                                                    $selected = $proxima_forma_contacto_id == $id;
                                                @endphp
                                                <button
                                                    type="button"
                                                    wire:click="$set('proxima_forma_contacto_id', {{ $id }})"
                                                    class="flex flex-col items-center justify-center p-3 rounded-md transition-all h-24 w-full
                                                        {{ $selected ? 'ring-2 ring-offset-2 ring-blue-500 bg-blue-500 text-white' : $config['color'] . ' text-gray-700 ' . $config['hover'] }}"
                                                    title="{{ $config['nombre'] }}">
                                                    @switch($config['svg'])
                                                        @case('mail')
                                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                                <path d="M4 4h16v16H4z" stroke="none"/>
                                                                <path d="M4 4l8 8 8-8" />
                                                            </svg>
                                                            @break
                                                        @case('globe')
                                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                                <path d="M12 2a10 10 0 100 20 10 10 0 000-20z" />
                                                                <path d="M2 12h20M12 2a15 15 0 010 20" />
                                                            </svg>
                                                            @break
                                                        @case('phone')
                                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                                <path d="M3 5a2 2 0 012-2h2l2 5-3 3a15 15 0 006 6l3-3 5 2v2a2 2 0 01-2 2h-1c-8.28 0-15-6.72-15-15V5z" />
                                                            </svg>
                                                            @break
                                                        @case('chat')
                                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                                <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" />
                                                            </svg>
                                                            @break
                                                    @endswitch
                                                    <span class="text-xs font-medium mt-1">{{ $config['nombre'] }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Fecha y hora próxima --}}
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Fecha próxima tarea</label>
                                            <input type="date" wire:model="proxima_fecha" class="w-full mt-1 p-2 border rounded">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Hora próxima tarea</label>
                                            <input type="time" wire:model="proxima_hora" class="w-full mt-1 p-2 border rounded">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Botones --}}
                        <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                            <button type="button" wire:click="cerrarModalRealizarTarea"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                                Cancelar
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Guardar Tarea
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>