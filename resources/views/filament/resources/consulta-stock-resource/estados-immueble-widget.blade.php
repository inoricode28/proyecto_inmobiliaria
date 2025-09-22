<div class="w-[85vw] h-screen overflow-y-auto bg-white rounded-lg shadow border p-6 lg:p-11" x-data="imagenViewer()" x-init="init()">

<div class="flex flex-wrap mb-4 gap-2 items-end">
    <!-- Combo Box para Tipo de Inmueble -->
    <div class="w-60">
        <label for="tipo-inmueble-filter" class="block text-sm font-medium text-gray-700">Tipo de Inmueble</label>
        <select id="tipo-inmueble-filter"
                wire:model="selectedTipoInmueble"
                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
            <option value="">Seleccione</option>
            @foreach($tiposInmueble as $tipo)
                <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
            @endforeach
        </select>
    </div>

    <!-- Combo Box para Proyecto -->
    <div class="w-60">
        <label for="proyecto-filter" class="block text-sm font-medium text-gray-700">Proyecto</label>
        <select id="proyecto-filter"
                wire:model="selectedProyecto"
                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
            <option value="">Todos los proyectos</option>
            @foreach($proyectos as $proyecto)
                <option value="{{ $proyecto->id }}">{{ $proyecto->nombre }}</option>
            @endforeach
        </select>
    </div>
</div>


    <!-- Sección de Financiamiento primero -->
    <div class="mb-4">
    <div class="flex items-center w-full px-2 py-2">
        <span class="font-bold text-gray-700 mr-3 text-sm w-[120px] flex-shrink-0">
            Tipos de financiamiento
        </span>
        <div class="flex flex-wrap gap-3">
            @foreach($this->getFinanciamientosData() as $tipo)
                <button class="inline-flex flex-col w-auto h-full overflow-hidden rounded-lg" title="{{ $tipo['descripcion'] }}">
                    <div class="h-2.5 w-full rounded-t-lg" style="background-color: {{ $tipo['color'] }}"></div>
                    <div class="bg-gray-50 px-3 py-1 border border-gray-200 flex flex-col items-center rounded-b-lg">
                        <div class="text-[10px] font-medium text-gray-700 text-center">
                            {{ $tipo['nombre'] }}
                        </div>
                    </div>
                </button>
            @endforeach
        </div>
    </div>
</div>

   <div class="mb-4">
    <div class="flex items-center w-full px-2 py-2">
        <span class="font-bold text-gray-700 mr-3 text-sm w-[90px] flex-shrink-0">
            Estados de inmueble
        </span>
        <div class="flex flex-wrap gap-3">
            @foreach($this->getEstadosData() as $estado)
                <button class="inline-flex flex-col w-auto h-full overflow-hidden rounded-lg" title="{{ $estado['descripcion'] }}">
                    <div class="h-2.5 w-full rounded-t-lg" style="background-color: {{ $estado['color'] }}"></div>
                    <div class="bg-gray-50 px-3 py-1 border border-gray-200 flex flex-col items-center rounded-b-lg">
                        <div class="text-[10px] font-medium text-gray-700 text-center">
                            {{ $estado['nombre'] }}
                        </div>
                    </div>
                </button>
            @endforeach
        </div>
    </div>


    <!-- Sección de Pisos -->
    <div class="bg-white rounded-lg shadow border p-4">
            <h3 class="font-bold text-lg mb-4">
                Disponibilidad por Pisos
                @if($selectedProyecto)
                    {{ $proyectos->firstWhere('id', $selectedProyecto)?->nombre }}
                @endif
            </h3>

    @foreach($this->getPisosData() as $piso => $departamentos)
        <div class="mb-2 flex items-start ml-6">
            <div class="w-10 h-10 flex flex-col items-center justify-center bg-black text-white text-center rounded mr-1 p-1">
                <span class="text-[9px] font-bold leading-tight">PISO</span>
                <span class="text-sm font-bold leading-none mt-0.5">{{ $piso }}</span>
            </div>
                                                                                                                                  
            <div class="flex">
                @foreach($departamentos as $departamento)                    
                    <div class="group relative w-16 h-16 border-r-2 border-b-2 cursor-pointer"
                        style="background-color: {{ $departamento->estadoDepartamento->color }};"
                        @click="
                            if ('{{ $departamento->estadoDepartamento->nombre }}' === 'Separacion' || '{{ $departamento->estadoDepartamento->nombre }}' === 'Minuta' || '{{ $departamento->estadoDepartamento->nombre }}' === 'Entregado') {
                                @php
                                    $proformaConSeparacion = $departamento->proformas()->whereHas('separacion')->latest()->first();
                                @endphp
                                @if($proformaConSeparacion)
                                    window.location.href = '/Proforma/DetalleProforma/{{ $proformaConSeparacion->id }}';
                                @else
                                    alert('No se encontró una proforma con separación para este departamento.');
                                @endif
                            }
                        ">
                        
                        <!-- Número -->
                        <div class="text-black text-sm font-bold text-center"
                            style="background-color: {{ $departamento->tipoFinanciamiento->color ?? '#D1D5DB' }};">
                            {{ $departamento->num_departamento }}
                        </div>

                        <!-- Ícono imagen clickeable -->
                        <div class="absolute inset-0 flex items-center justify-center pt-4">
                            @if($departamento->fotoDepartamentos->isNotEmpty())
                                <img src="{{ asset('img/logotipo.png') }}"
                                        alt="ver imagen"
                                        class="w-5 h-5 hover:scale-125 transition"
                                        @click="$dispatch('abrirModalFotos', {
                                            fotos: {{ json_encode($departamento->fotoDepartamentos->pluck('imagen')->map(fn($img) => asset('storage/' . $img))) }}
                                        })">
                            @else
                                <img src="{{ asset('images/icons/imagen.png') }}"
                                        alt="sin imagen"
                                        class="w-5 h-5 opacity-50">
                            @endif
                        </div>

                        <!-- Tooltip actualizado -->
                        <div class="absolute z-50 hidden group-hover:block w-80 text-left bg-white text-black border border-gray-400 shadow-lg p-4 rounded-md top-1/2 -translate-y-1/2 left-full ml-1 text-xs">
                            <div class="font-bold text-sm mb-2">Departamento Nro. {{ $departamento->num_departamento }}</div>

                            <div class="mb-2">
                                <div><strong>Tipo moneda:</strong> {{ $departamento->moneda?->nombre ?? 'Sin Data' }}</div>
                                <div><strong>Precio:</strong> S/ {{ number_format($departamento->precio, 2) }}</div>
                                <div><strong>Precio xm2:</strong> {{ $departamento->precio && $departamento->construida ? number_format($departamento->precio / $departamento->construida, 2) : '0' }}</div>
                            </div>

                            <div class="mb-2">
                                <div><strong>Modelo:</strong> {{ $departamento->tipoDepartamento?->nombre ?? '' }}</div>
                                <div><strong>Vista:</strong> {{ $departamento->vista?->nombre ?? '' }}</div>
                                <div><strong>Nro Hab:</strong> {{ $departamento->num_dormitorios }}</div>
                                <div><strong>Nro Baños:</strong> {{ $departamento->num_bano }}</div>
                                <div><strong>Área TOTAL:</strong> {{ $departamento->construida }}m2</div>
                                <div class="text-xs">
                                    <em>Otras áreas: <strong>Techada:</strong> {{ $departamento->techada }}m2 | <strong>Construida:</strong> {{ $departamento->construida }}m2</em>
                                </div>
                            </div>

                            <hr class="my-2 border-gray-300">

                            <div class="text-xs font-semibold mb-1">INMUEBLES EN OPERACIÓN</div>
                            <div class="mb-2">
                                <div>Departamento-{{ $departamento->num_departamento }}</div>
                                <div><strong>Precio:</strong> 5/ {{ number_format($departamento->precio, 2) }}</div>
                            </div>

                            <div class="mb-2">
                                <div><strong>Tipo financiamiento:</strong> {{ $departamento->tipoFinanciamiento?->nombre ?? '---' }}</div>
                                <div><strong>Estado:</strong> {{ $departamento->estadoDepartamento->nombre }}</div>
                                <div><strong>Motivo Bloqueo:</strong> {{ $departamento->observaciones ?: 'Ninguno' }}</div>
                                <div><strong>Fecha:</strong> {{ now()->format('d/m/Y') }}</div>
                                <div><strong>Cliente:</strong> {{ $departamento->cliente?->nombre_completo ?? 'Sin asignar' }}</div>
                            </div>

                            <div>
                                <strong>Asesor comercial:</strong> {{ $departamento->asesor?->nombre ?? 'Sin asignar' }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach 
</div>

<div class="p-4 bg-white rounded-lg shadow mb-6">
    <h2 class="text-lg font-medium text-gray-900">Estado Inmueble: {{ $edificio?->nombre ?? 'Sin edificio seleccionado' }}</h2>

    <div class="overflow-x-auto mt-4 max-h-[80vh] overflow-y-auto"> <!-- Añade scroll vertical -->
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 sticky top-0"> <!-- Cabecera fija -->
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-white">
                        Departamento
                    </th>
                    @foreach($estados as $estado)
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-white">
                            {{ $estado->nombre }}
                        </th>
                    @endforeach
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-white">
                        Total
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($rows as $row)
                <tr class="{{ $loop->last ? 'bg-gray-100 font-bold' : '' }}">
                    <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $row['name'] }}
                    </td>

                    @foreach($estados as $estado)
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-center text-gray-500">
                            {{ $row[$estado->id] ?? 0 }}
                        </td>
                    @endforeach

                    <td class="px-4 py-2 whitespace-nowrap text-sm text-center font-bold text-gray-900">
                        {{ $row['totals'] }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

    <!-- Modal de imágenes fuera del contenido principal -->
    <div x-show="imagenModal"
        x-cloak
        class="fixed inset-0 z-50 bg-black bg-opacity-80 flex items-center justify-center"
        @click.self="imagenModal = null"
        x-transition>
        <div class="bg-white p-6 rounded-xl shadow-lg max-w-6xl w-full max-h-[90vh] overflow-y-auto">
            <button @click="imagenModal = null" 
                    class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            
            <template x-for="src in imagenModal" :key="src">
                <img :src="src"
                    alt="Foto Departamento"
                    class="w-full max-h-[80vh] object-contain mb-4 rounded shadow-lg mx-auto">
            </template>
        </div>
    </div>
</div>

<script>
    function imagenViewer() {
        return {
            imagenModal: null,
            
            init() {
                window.addEventListener('abrirModalFotos', (event) => {
                    this.imagenModal = event.detail.fotos;
                });
            }
        }
    }
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
