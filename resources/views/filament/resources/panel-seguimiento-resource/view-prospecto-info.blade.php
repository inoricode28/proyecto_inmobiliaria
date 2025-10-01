@php
use App\Filament\Resources\Proforma\ProformaResource; // <- Ajusta el namespace si tu recurso vive en otro subnamespace
    @endphp <x-filament::page>
    <h2 class="text-2xl font-bold mb-4">Información del Prospecto</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 p-4 bg-gray-100 rounded-lg shadow-sm">
        <div>
            <strong>Nombre Completo:</strong>
            {{ $prospecto->nombres }} {{ $prospecto->ape_paterno }} {{ $prospecto->ape_materno }}
        </div>
        <div>
            <strong>Tipo Documento:</strong> {{ optional($prospecto->tipoDocumento)->nombre }}
        </div>
        <div>
            <strong>N° Documento:</strong> {{ $prospecto->numero_documento }}
        </div>
        <div>
            <strong>Razón Social:</strong> {{ $prospecto->razon_social }}
        </div>
        <div>
            <strong>Celular:</strong> {{ $prospecto->celular }}
        </div>
        <div>
            <strong>Correo Electrónico:</strong> {{ $prospecto->correo_electronico }}
        </div>
        <div>
            <strong>Proyecto:</strong> {{ optional($prospecto->proyecto)->nombre }}
        </div>
        <div>
            <strong>Tipo de Inmueble:</strong> {{ optional($prospecto->tipoInmueble)->nombre }}
        </div>
        <div>
            <strong>Forma de Contacto:</strong> {{ optional($prospecto->formaContacto)->nombre }}
        </div>
        <div>
            <strong>¿Cómo se enteró?:</strong> {{ optional($prospecto->comoSeEntero)->nombre }}
        </div>
        <div>
            <strong>Tipo de Gestión:</strong> {{ optional($prospecto->tipoGestion)->nombre }}
        </div>
        <div>
            <strong>Registrado por:</strong> {{ optional($prospecto->creador)->name }}
        </div>
        <div>
            <strong>Fecha de Registro:</strong>
            {{ \Carbon\Carbon::parse($prospecto->fecha_registro)->format('d/m/Y H:i') }}
        </div>
    </div>

    {{-- Sección de Proformas del Prospecto --}}
    @if($prospecto->proformas->isNotEmpty())
    <div class="mt-8 mb-6">
        <x-filament::card>
            <x-slot name="heading">
                Operaciones Comerciales
            </x-slot>

            <div class="space-y-4">
                @foreach($prospecto->proformas as $proforma)
                    <div class="border border-gray-300 rounded-lg overflow-hidden bg-white">
                        <!-- Header colapsible -->
                        <div class="bg-gray-100 px-4 py-3 cursor-pointer hover:bg-gray-200 transition-colors" 
                             onclick="toggleProforma('proforma-{{ $proforma->id }}')">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <span class="text-blue-600 font-medium" id="arrow-{{ $proforma->id }}">▼</span>
                                    <a href="/Proforma/DetalleProforma/{{ $proforma->id }}" 
                                        target="_blank"
                                        class="text-blue-600 hover:text-blue-800 font-medium"
                                        onclick="event.stopPropagation()">
                                         Proforma N° {{ str_pad($proforma->id, 6, '0', STR_PAD_LEFT) }} - {{ $proforma->created_at->format('d/m/Y H:i') }}
                                     </a>
                                </div>
                            </div>
                        </div>

                        <!-- Contenido colapsible -->
                        <div id="proforma-{{ $proforma->id }}" class="px-4 py-3 border-t border-gray-200">
                            <div class="text-sm text-gray-700 space-y-1">
                                <div><strong>Proyecto:</strong> {{ $proforma->proyecto->nombre ?? 'N/A' }}</div>
                                <div><strong>Vendedor:</strong> {{ $proforma->prospecto->nombres ?? 'N/A' }} {{ $proforma->prospecto->ape_paterno ?? '' }}</div>
                                
                                <!-- Inmuebles -->
                                @if($proforma->proformaInmuebles && $proforma->proformaInmuebles->count() > 0)
                                    @foreach($proforma->proformaInmuebles as $proformaInmueble)
                                        @if($proformaInmueble->departamento)
                                            <div class="ml-4 mt-2">
                                                <div class="text-gray-800">
                                                    • <strong>Edificio:</strong> {{ $proformaInmueble->departamento->edificio->nombre ?? 'N/A' }} - <strong>Departamento N°:</strong> {{ $proformaInmueble->departamento->num_departamento }}
                                                </div>
                                                <div class="text-gray-600 text-sm ml-2">
                                                    Proyecto: {{ $proformaInmueble->departamento->proyecto->nombre ?? 'N/A' }}
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @elseif($proforma->departamento)
                                    <!-- Fallback para proformas que usan el campo departamento_id directamente -->
                                    <div class="ml-4 mt-2">
                                        <div class="text-gray-800">
                                            • <strong>Edificio:</strong> {{ $proforma->departamento->edificio->nombre ?? 'N/A' }} - <strong>Departamento N°:</strong> {{ $proforma->departamento->num_departamento }}
                                        </div>
                                       
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <script>
                function toggleProforma(id) {
                    const content = document.getElementById(id);
                    const arrow = document.getElementById('arrow-' + id.split('-')[1]);
                    
                    if (content.style.display === 'none') {
                        content.style.display = 'block';
                        arrow.textContent = '▼';
                    } else {
                        content.style.display = 'none';
                        arrow.textContent = '▶';
                    }
                }
                
                // Inicializar todos los elementos como expandidos
                document.addEventListener('DOMContentLoaded', function() {
                    @foreach($prospecto->proformas as $proforma)
                        document.getElementById('proforma-{{ $proforma->id }}').style.display = 'block';
                    @endforeach
                });
            </script>
        </x-filament::card>
    </div>
    @else
    <div class="mt-8 mb-6">
        <x-filament::card>
            <x-slot name="heading">
                Operaciones Comerciales
            </x-slot>
            <div class="text-center py-8 text-gray-500">
                <p>No hay proformas asociadas a este prospecto.</p>
            </div>
        </x-filament::card>
    </div>
    @endif

    <div class="flex flex-wrap gap-3 mb-6">
        <a href="{{ ProformaResource::getUrl('create', ['numero_documento' => $prospecto->numero_documento]) }}"
            target="_blank"
            class="inline-flex items-center px-4 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 transition">
            Proformar
        </a>

        <!--
        <button type="button" disabled
            class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-400 text-white cursor-not-allowed"
            title="Próximamente">
            Reasignar
        </button>
        -->
        <button type="button" wire:click="abrirModalReasignacion"
            class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-600 text-white hover:bg-orange-700 transition">
            Reasignar
        </button>

        <button type="button" wire:click="abrirModalRealizarTarea"
            class="inline-flex items-center px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition">
            Realizar Tarea
        </button>

        <button type="button" wire:click="abrirModalAgendarCita"
            class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
            Realizar Accion
        </button>

        @php
            $proforma = $prospecto->proformas->first();
        @endphp

        @if($proforma)
            <a href="{{ route('separacion-definitiva.create', ['proforma_id' => $proforma->id]) }}"
                class="inline-flex items-center px-4 py-2 rounded-lg bg-purple-600 text-white hover:bg-purple-700 transition">
                Separación Definitiva
            </a>
        @else
            <a href="{{ route('separacion-definitiva.create', ['numero_documento' => $prospecto->numero_documento]) }}"
                class="inline-flex items-center px-4 py-2 rounded-lg bg-purple-600 text-white hover:bg-purple-700 transition">
                Separación Definitiva
            </a>
        @endif

    </div>

    {{-- Mostrar errores de validación --}}
    @if ($errors->any())
    <div class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if ($ultimaTareaPendiente)
    <div class="mt-6 p-4 bg-yellow-100 border-l-4 border-yellow-500 rounded-lg">
        <div class="text-lg font-semibold text-yellow-800 mb-2">📌 Próxima Tarea</div>

        <div class="text-base text-gray-800">
            <strong class="text-blue-700">
                {{ strtoupper(optional($ultimaTareaPendiente->formaContacto)->nombre ?? 'SIN CONTACTO') }}
                -
                Fecha: {{ \Carbon\Carbon::parse($ultimaTareaPendiente->fecha_realizar)->format('d/m/Y') }}
                {{ $ultimaTareaPendiente->hora }}
            </strong>
        </div>

        <div class="text-sm text-gray-700 mt-1">
            Interés: <strong
                class="uppercase">{{ optional($ultimaTareaPendiente->nivelInteres)->nombre ?? '-' }}</strong> -
            Responsable: <strong>{{ optional($ultimaTareaPendiente->usuarioAsignado)->name ?? '-' }}</strong>
        </div>
    </div>
    @endif

    <h3 class="text-xl font-semibold mt-6 mb-2">Historial de Tareas / Citas</h3>
    <div class="space-y-6 mt-4">
        @forelse ($prospecto->tareas->sortByDesc('created_at') as $tarea)
        <div class="bg-white p-4 rounded-xl shadow-sm border">
            <div class="text-lg font-semibold text-gray-800">
                {{ strtoupper(optional($tarea->formaContacto)->nombre ?? 'SIN CONTACTO') }} -
                <span class="text-sm font-normal text-gray-500">
                    Fecha y Hora: {{ \Carbon\Carbon::parse($tarea->fecha_realizar)->format('d/m/Y') }}
                    {{ $tarea->hora }}
                </span>
            </div>

            <div class="mt-2 text-sm text-gray-700">
                Nivel interés:
                <strong class="uppercase">{{ optional($tarea->nivelInteres)->nombre ?? '-' }}</strong> -
                Responsable:
                <strong>{{ optional($tarea->usuarioAsignado)->name ?? '-' }}</strong>
            </div>

            <div class="mt-3 text-sm text-gray-800">
                <div><strong>Respuesta:</strong> ( {{ $tarea->respuesta ?? 'No especificada' }} )</div>
                <div class="mt-2">
                    <strong>Comentario :</strong><br>
                    {{ $tarea->nota ?? 'Sin comentarios' }}
                </div>
            </div>
        </div>
        @empty
        <div class="text-gray-500">No hay tareas registradas.</div>
        @endforelse
    </div>

    {{-- Incluir los modales como componentes separados --}}
    @include('filament.resources.panel-seguimiento-resource.components.realizar-accion-modal')
    @include('filament.resources.panel-seguimiento-resource.components.agendar-cita-modal')
    @include('filament.resources.panel-seguimiento-resource.components.reasignacion-contacto-modal')

    </x-filament::page>