<div>
    {{-- TITULO --}}
    <h4 class="flex items-center ms-2 text-2xl font-bold dark:text-white">HISTORIAL PRODUCCION</h4>
    {{-- BARA PRINCIPAL DE BUSQUEDA --}}
    <div class="flex gap-3 items-end">
        {{-- INPUT MES --}}
        <div>
            <label for="name" class="block mb-1 text-base font-medium text-gray-900 dark:text-white">
                Buscar por mes:</label>
            <input type="month" wire:model='mes_busqueda'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
        </div>
        {{-- Barra de busqueda --}}
        <div class="w-64">
            <livewire:search-bar tittle="Buscar insumo" table="insumos" :columns="['clave', 'descripcion']" primary="clave"
                event="selected-articulo" />
        </div>
        {{-- boton de busqueda --}}
        <div>
            <button type="button" wire:click='actualizar'
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                <svg wire:loading.remove wire:target='actualizar' class="w-6 h-6" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                        d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                </svg>
                <!--Loading indicator-->
                <div wire:loading wire:target='actualizar'>
                    @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
                </div>
                <span class="sr-only">Buscar</span>
            </button>
        </div>
    </div>
    {{-- TABLA RESULTADOS --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
            wire:loading.class='animate-pulse' wire:target='editableReceta,eliminarArticulo'>
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-3 py-2">
                        PROD.
                    </th>
                    <th scope="col" class="px-3 py-2">
                        F.EXISTENCIA
                    </th>
                    <th scope="col" class="px-3 py-2">
                        #
                    </th>
                    <th scope="col" class="px-3 py-2">
                        INSUM.ELABORADO
                    </th>
                    <th scope="col" class="px-3 py-2">
                        CANTIDAD
                    </th>
                    <th scope="col" class="px-3 py-2">
                        RENDIMIENTO
                    </th>
                    <th scope="col" class="px-3 py-2">
                        TOTAL PROD.
                    </th>
                    <th scope="col" class="px-3 py-2">
                        ORIGEN
                    </th>
                    <th scope="col" class="px-3 py-2">
                        DESTINO
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->producciones as $i => $row)
                    <tr wire:key='{{ $i }}'
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <th scope="row"
                            class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $row->folio_transformacion }}
                        </th>
                        <td class="px-3 py-2">
                            {{ $row->transformacion->fecha_existencias }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $row->clave_insumo_elaborado }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $row->insumoElaborado->descripcion }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $row->cantidad }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $row->rendimiento }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $row->total_elaborado }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $row->transformacion?->origen->descripcion }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $row->transformacion?->destino->descripcion }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div>
        {{ $this->producciones->links() }}
    </div>
</div>
