@php
    use App\Filament\Resources\Proforma\ProformaResource; // <- Ajusta el namespace si tu recurso vive en otro subnamespace
@endphp
<x-filament::page>
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
            <strong>Fecha de Registro:</strong> {{ \Carbon\Carbon::parse($prospecto->fecha_registro)->format('d/m/Y H:i') }}
        </div>
    </div>

    <div class="flex flex-wrap gap-3 mb-6">
        <a
            href="{{ ProformaResource::getUrl('create', ['numero_documento' => $prospecto->numero_documento]) }}"
            target="_blank"
            class="inline-flex items-center px-4 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 transition"
        >
            Proformar
        </a>

        <button
            type="button"
            disabled
            class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-400 text-white cursor-not-allowed"
            title="Próximamente"
        >
            Reasignar
        </button>

        <button
            type="button"
            class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition"
        >
            Realizar Acción
        </button>
    </div>

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
                Interés: <strong class="uppercase">{{ optional($ultimaTareaPendiente->nivelInteres)->nombre ?? '-' }}</strong> -
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
                        Fecha y Hora: {{ \Carbon\Carbon::parse($tarea->fecha_realizar)->format('d/m/Y') }} {{ $tarea->hora }}
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

</x-filament::page>
