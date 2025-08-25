{{-- Modal de Reasignación de Contacto --}}
<div x-data="{ show: @entangle('showReasignacionModal') }" x-show="show" x-cloak
    class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        {{-- Overlay --}}
        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        {{-- Modal --}}
        <div x-show="show" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block align-bottom bg-gray-50 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">

            <div class="bg-gray-50 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="w-full">
                        {{-- Header --}}
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Reasignación de Contacto
                            </h3>
                            <button wire:click="cerrarModalReasignacion" type="button"
                                class="text-gray-400 hover:text-gray-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- Formulario --}}
                        <div class="space-y-6">
                            {{-- Proyecto y Responsable --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Proyecto</label>
                                    <input type="text" value="{{ $prospecto->proyecto->nombre ?? 'N/A' }}"
                                        class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Responsable <span
                                            class="text-red-500">*</span></label>
                                    <select wire:model="reasignacion_responsable_id"
                                        class="w-full p-3 border rounded-lg" required>
                                        <option value="">Seleccionar responsable</option>
                                        @foreach(\App\Models\User::all() as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('reasignacion_responsable_id') <span
                                        class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Tarea Nueva --}}
                            {{-- Forma de contacto --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Forma de contacto</label>
                                @php
                                $formasPermitidas = [
                                1 => ['nombre' => 'Correo', 'svg' => 'mail', 'color' => 'bg-blue-100', 'hover' =>
                                'hover:bg-blue-200'],
                                2 => ['nombre' => 'Facebook', 'svg' => 'globe', 'color' => 'bg-indigo-100', 'hover' =>
                                'hover:bg-indigo-200'],
                                8 => ['nombre' => 'Teléfono', 'svg' => 'phone', 'color' => 'bg-green-100', 'hover' =>
                                'hover:bg-green-200'],
                                9 => ['nombre' => 'WhatsApp', 'svg' => 'chat', 'color' => 'bg-lime-100', 'hover' =>
                                'hover:bg-lime-200'],
                                ];
                                @endphp
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    @foreach($formasPermitidas as $id => $config)
                                    @php
                                    $selected = $forma_contacto_id == $id;
                                    @endphp
                                    <button type="button" wire:click="$set('forma_contacto_id', {{ $id }})"
                                        class="flex flex-col items-center justify-center p-3 rounded-md transition-all h-24 w-full
                                                    {{ $selected ? 'ring-2 ring-offset-2 ring-blue-500 bg-blue-500 text-white' : $config['color'] . ' text-gray-700 ' . $config['hover'] }}"
                                        title="{{ $config['nombre'] }}">
                                        @switch($config['svg'])
                                        @case('mail')
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8"
                                            viewBox="0 0 24 24">
                                            <path d="M4 4h16v16H4z" stroke="none" />
                                            <path d="M4 4l8 8 8-8" />
                                        </svg>
                                        @break
                                        @case('globe')
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8"
                                            viewBox="0 0 24 24">
                                            <path d="M12 2a10 10 0 100 20 10 10 0 000-20z" />
                                            <path d="M2 12h20M12 2a15 15 0 010 20" />
                                        </svg>
                                        @break
                                        @case('phone')
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M3 5a2 2 0 012-2h2l2 5-3 3a15 15 0 006 6l3-3 5 2v2a2 2 0 01-2 2h-1c-8.28 0-15-6.72-15-15V5z" />
                                        </svg>
                                        @break
                                        @case('chat')
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8"
                                            viewBox="0 0 24 24">
                                            <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" />
                                        </svg>
                                        @break
                                        @endswitch
                                        <span class="text-xs font-medium mt-1">{{ $config['nombre'] }}</span>
                                    </button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Nivel de Interés --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">Nivel de Interés</label>
                                <div class="flex flex-wrap gap-2">
                                    @php
                                    $nivelesInteres = [
                                    ['id' => 1, 'nombre' => 'BAJO'],
                                    ['id' => 2, 'nombre' => 'PRE CALIFICACION'],
                                    ['id' => 3, 'nombre' => 'DERIVADO BANCO'],
                                    ['id' => 4, 'nombre' => 'POTENCIAL'],
                                    ['id' => 5, 'nombre' => 'SEGUIMIENTO']
                                    ];
                                    @endphp

                                    @foreach($nivelesInteres as $nivel)
                                    <button type="button"
                                        wire:click="$set('reasignacion_nivel_interes_id', {{ $nivel['id'] }})" class="px-4 py-2 rounded-md text-sm font-medium transition-colors
                                            {{ $reasignacion_nivel_interes_id == $nivel['id']
                                                ? 'bg-cyan-500 text-white'
                                                : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                        {{ $nivel['nombre'] }}
                                    </button>
                                    @endforeach
                                </div>
                                @error('reasignacion_nivel_interes_id') <span
                                    class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            {{-- Fecha y Hora --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Tarea</label>
                                    <input type="date" wire:model="reasignacion_fecha_tarea"
                                        class="w-full p-3 border rounded-lg" required>
                                    @error('reasignacion_fecha_tarea') <span
                                        class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Hora Tarea</label>
                                    <input type="time" wire:model="reasignacion_hora_tarea"
                                        class="w-full p-3 border rounded-lg" required>
                                    @error('reasignacion_hora_tarea') <span
                                        class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Comentario --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Comentario</label>
                                <textarea wire:model="reasignacion_comentario" rows="4"
                                    class="w-full p-3 border rounded-lg"
                                    placeholder="Comentario de reasignación..."></textarea>
                                @error('reasignacion_comentario') <span
                                    class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button wire:click="reasignarContacto" type="button"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Grabar
                </button>
                <button wire:click="cerrarModalReasignacion" type="button"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>
