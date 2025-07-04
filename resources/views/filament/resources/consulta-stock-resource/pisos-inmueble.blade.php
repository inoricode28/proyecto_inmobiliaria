<div x-data="{ imagenModal: null }" class="bg-white rounded-lg shadow border p-4">
    <h3 class="font-bold text-lg mb-4">Disponibilidad por Pisos</h3>

    @foreach($this->getPisosData() as $piso => $departamentos)
        <div class="mb-4 flex items-start">
            <div class="w-20 h-full flex items-center justify-center bg-black text-white font-bold rounded mr-2 py-2">
                Piso {{ $piso }}
            </div>

            <div class="flex">
                @foreach($departamentos as $departamento)
                    <div class="group relative w-16 h-16 border-r-2 border-b-2 cursor-pointer"
                         style="background-color: {{ $departamento->estadoDepartamento->color }};">

                        {{-- Número --}}
                        <div class="bg-gray-300 text-black text-sm font-bold text-center">
                            {{ $departamento->num_departamento }}
                        </div>

                        {{-- Ícono imagen clickeable --}}
                        <div class="absolute inset-0 flex items-center justify-center pt-4">
                            @if($departamento->fotoDepartamentos->isNotEmpty())
                                <img src="{{ asset('img/logotipo.png') }}"
                                     alt="ver imagen"
                                     class="w-5 h-5 hover:scale-125 transition"
                                     @click="imagenModal = {{ json_encode($departamento->fotoDepartamentos->pluck('imagen')->map(fn($img) => asset('storage/' . $img))) }}">
                            @else
                                <img src="{{ asset('images/icons/imagen.png') }}"
                                     alt="sin imagen"
                                     class="w-5 h-5 opacity-50">
                            @endif
                        </div>

                        {{-- Tooltip actualizado --}}
                        <div class="absolute z-50 hidden group-hover:block w-80 text-left bg-white text-black border border-gray-400 shadow-lg p-4 rounded-md top-full mt-1 left-1/2 -translate-x-1/2 text-xs">
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
                                <div><strong>Tipo financiamiento:</strong> Crédito Directo</div>
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

    {{-- Modal de imágenes --}}
    <div x-show="imagenModal"
        class="fixed inset-0 z-50 bg-black bg-opacity-80 flex items-center justify-center"
        @click.self="imagenModal = null"
        x-transition>
        <div class="bg-white p-6 rounded-xl shadow-lg max-w-6xl w-full max-h-[90vh] overflow-y-auto">
            <template x-for="src in imagenModal" :key="src">
                <img :src="src"
                    alt="Foto Departamento"
                    class="w-full max-h-[80vh] object-contain mb-4 rounded shadow-lg mx-auto">
            </template>
        </div>
    </div>
</div>
