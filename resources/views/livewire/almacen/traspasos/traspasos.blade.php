<div>
    {{-- Search bar --}}
    <form class="flex gap-4 items-end ms-3 w-40" method="GET" wire:submit='buscar'>
        @csrf
        <div>
            <label for="name" class="block  text-base font-medium text-gray-900 dark:text-white">
                Buscar por mes:</label>
            <input type="date" wire:model='mes_busqueda'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
        </div>
        <div>
            <button type="submit" wire:click='buscar'
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                <svg wire:loading.remove wire:target='buscar' class="w-6 h-6" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                        d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                </svg>
                <!--Loading indicator-->
                <div wire:loading wire:target='buscar'>
                    @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
                </div>
                <span class="sr-only">Buscar</span>
            </button>
        </div>
    </form>
    {{-- Tabla --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        FOLIO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        FECHA
                    </th>
                    <th scope="col" class="px-6 py-3">
                        RESPONSABLE
                    </th>
                    <th scope="col" class="px-6 py-3">
                        NO.MOVIMIENTOS
                    </th>
                    <th scope="col" class="px-6 py-3 w-5/12">
                        OBSERVACIONES
                    </th>
                    <th scope="col" class="px-6 py-3">
                        ACCIONES
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->traspasos as $index => $traspaso)
                    <tr wire:key='{{ $index }}'
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-2">
                            {{ $traspaso->folio }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $traspaso->created_at }}
                        </td>
                        <td class="px-6 py-3">
                            {{ $traspaso->user->name }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $traspaso->movimientos }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $traspaso->observaciones }}
                        </td>
                        <td class="px-6 py-2">
                            <div class="flex">
                                <div>
                                    <a type="button" wire:click='showDetails({{ $traspaso->folio }})'
                                        class="text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2 text-center inline-flex items-blue me-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500">
                                        <div wire:loading.remove wire:target='showDetails({{ $traspaso->folio }})' class="flex">
                                            <svg class="w-5 h-5 aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd"
                                                    d="M8 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1h2a2 2 0 0 1 2 2v15a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h2Zm6 1h-4v2H9a1 1 0 0 0 0 2h6a1 1 0 1 0 0-2h-1V4Zm-6 8a1 1 0 0 1 1-1h6a1 1 0 1 1 0 2H9a1 1 0 0 1-1-1Zm1 3a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2H9Z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Ver Lista
                                        </div>
                                        <!--Loading indicator-->
                                        <div wire:loading wire:target='showDetails({{ $traspaso->folio }})'>
                                            @include('livewire.utils.loading', ['w' => 5, 'h' => 5])
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{-- Paginador --}}
    <div>
        {{ $this->traspasos->links() }}
    </div>
    {{-- MODAL DE INFORMACION DE TRASPASO --}}
    <x-modal name="modal-traspaso" title="DETALLES TRASPASO">
        <x-slot name='body'>
            <p>Folio del traspaso: {{ $traspaso_seleccionado }}</p>
            <div class="relative max-h-96 overflow-y-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-2">
                                Producto
                            </th>
                            <th scope="col" class="px-6 py-2">
                                Nombre
                            </th>
                            <th scope="col" class="px-6 py-2">
                                Cantidad
                            </th>
                            <th scope="col" class="px-6 py-2">
                                Peso
                            </th>
                            <th scope="col" class="px-6 py-2">
                                Origen
                            </th>
                            <th scope="col" class="px-6 py-2">
                                Destino
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->traspaso_detalles as $index => $detalle)
                            <tr wire:key='{{ $index }}'
                                class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                <th scope="row"
                                    class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $detalle->codigo_articulo }}
                                </th>
                                <td class="px-6 py-2 max-w-md">
                                    {{ $detalle->nombre }}
                                </td>
                                <td class="px-6 py-2">
                                    {{ $detalle->cantidad }}
                                </td>
                                <td class="px-6 py-2">
                                    {{ $detalle->peso }}
                                </td>
                                <td class="px-6 py-2">
                                    {{ $bodegas->find($detalle->clave_bodega_origen) ? $bodegas->find($detalle->clave_bodega_origen)->descripcion : '' }}
                                </td>
                                <td class="px-6 py-2">
                                    {{ $bodegas->find($detalle->clave_bodega_destino) ? $bodegas->find($detalle->clave_bodega_destino)->descripcion : '' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-slot>
    </x-modal>
</div>
