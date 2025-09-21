<div class="ms-3 mx-3">
    <div class="flex gap-3 items-end">
        {{-- FECHA --}}
        <div>
            <label class="block mb-1 text-base font-medium text-gray-900 dark:text-white">Buscar por Mes</label>
            <input type="month" wire:model='search_mes'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        </div>
        {{-- BODEGA DE ORIGEN --}}
        <div>
            <select id="origen" wire:model.live='clave_origen'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option selected value="{{ null }}">BODEGA ORIGEN</option>
                @foreach ($this->bodegas as $b)
                    <option value="{{ $b->clave }}">{{ $b->descripcion }}</option>
                @endforeach
            </select>
        </div>
        {{-- BODEGA DE DESTINO --}}
        <div>
            <select id="destino" wire:model.live='clave_destino'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option selected value="{{ null }}">BODEGA DESTINO</option>
                @foreach ($this->bodegas as $b)
                    <option value="{{ $b->clave }}">{{ $b->descripcion }}</option>
                @endforeach
            </select>
        </div>
        {{-- BOTÓN Y BARRA DE BUSQUEDA --}}
        <div class="flex w-96">
            @if ($tipo_articulo)
                <livewire:search-bar tittle="Buscar presentacion" table="presentaciones" :columns="['clave', 'descripcion']"
                    primary="clave" event="selected-articulo" />
            @else
                <livewire:search-bar tittle="Buscar insumo" table="insumos" :columns="['clave', 'descripcion']" primary="clave"
                    event="selected-articulo" />
            @endif
            <div>
                <button type="button" wire:click='buscar'
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center ms-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    <svg wire:loading.remove wire:target='buscar' class="w-5 h-5" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                            d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                    </svg>
                    <span class="sr-only">Buscar</span>
                    <!--Loading indicator-->
                    <div wire:loading wire:target='buscar'>
                        @include('livewire.utils.loading', ['w' => 5, 'h' => 5])
                    </div>
                    <span class="sr-only">Buscar</span>
                </button>
            </div>
        </div>
    </div>

    {{-- TABLA DE RESULTADOS --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg my-2">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        TRASPASO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        FECHA EXISTENCIAS
                    </th>
                    <th scope="col" class="px-6 py-3">
                        #
                    </th>
                    <th scope="col" class="px-6 py-3">
                        PRESENTACIÓN / INSUMO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        CANTIDAD
                    </th>
                    <th scope="col" class="px-6 py-3">
                        RENDIMIENTO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        CANTIDAD INSUMO
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->traspasos as $index => $detalles)
                    <tr wire:key='{{ $index }}'
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                        <th scope="row"
                            class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $detalles->folio_traspaso }}
                        </th>
                        <td class="px-6 py-2">
                            {{ $detalles->traspaso->fecha_existencias }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $detalles->clave_presentacion ?: $detalles->clave_insumo }}
                        </td>
                        <td class="px-6 py-2 w-80">
                            {{ $detalles->descripcion }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $detalles->cantidad }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $detalles->rendimiento }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $detalles->cantidad_insumo }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{-- PAGINADOR --}}
    <div>
        {{ $this->traspasos->links() }}
    </div>

    {{-- BOTON DE REGRESAR --}}
    <a type="button" href="{{ route('almacen.traspasov2') }}"
        class="my-2 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
        <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
            fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M5 12h14M5 12l4-4m-4 4 4 4" />
        </svg>
        Regresar
    </a>
</div>
