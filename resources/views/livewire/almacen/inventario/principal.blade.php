<div class="ms-3 mx-3">
    <div class="flex gap-2 items-end">
        {{-- FECHA MES --}}
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Buscar por Mes</label>
            <input datepicker type="month" wire:model='mes'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        </div>
        {{-- BODEGA --}}
        <form class="w-fit">
            <select id="bodega" wire:model='clave_bodega'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="{{ null }}">SELECCIONAR BODEGA</option>
                @foreach ($this->bodegas as $index => $item)
                    <option value="{{ $item->clave }}">{{ $item->descripcion }}</option>
                @endforeach
            </select>
        </form>
        {{-- BOTON DE BUSQUEDA --}}
        <button type="button" wire:click='buscar'
            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            <svg wire:loading.remove wire:target='buscar' class="w-5 h-5" aria-hidden="true"
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

    {{-- TABLA DE INVENTARIOS --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg my-2">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        FOLIO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        FECHA INVENTARIO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        HORA INVENTARIO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        BODEGA
                    </th>
                    <th scope="col" class="px-6 py-3">
                        OBSERVACIONES
                    </th>
                    <th scope="col" class="px-6 py-3">

                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->inventarios as $i => $item)
                    <tr wire:key='{{ $item->folio }}'
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                        <th scope="row"
                            class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $item->folio }}
                        </th>
                        <td class="px-6 py-2">
                            {{ substr($item->fecha_existencias, 0, 10) }}
                        </td>
                        <td class="px-6 py-2">
                            {{ substr($item->fecha_existencias, 11, 8) }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $item->bodega->descripcion }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $item->observaciones }}
                        </td>
                        <td class="px-6 py-2 text-center">
                            <div class="flex">
                                <div>
                                    <a type="button" wire:click='showDetails({{ $item->folio }})'
                                        class="text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-1.5 text-center inline-flex items-blue dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500">
                                        <div wire:loading.remove wire:target='showDetails({{ $item->folio }})'
                                            class="flex">
                                            <svg class="w-5 h-5 me-2" xmlns="http://www.w3.org/2000/svg" width="24"
                                                height="24" fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd"
                                                    d="M8 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1h2a2 2 0 0 1 2 2v15a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h2Zm6 1h-4v2H9a1 1 0 0 0 0 2h6a1 1 0 1 0 0-2h-1V4Zm-6 8a1 1 0 0 1 1-1h6a1 1 0 1 1 0 2H9a1 1 0 0 1-1-1Zm1 3a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2H9Z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Detalles
                                        </div>
                                        <!--Loading indicator-->
                                        <div wire:loading wire:target='showDetails({{ $item->folio }})'>
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
    <div>
        {{$this->inventarios->links()}}
    </div>

    {{-- MODAL GRUPOS PRESENTACIONES --}}
    <x-modal name="modal-detalles" title="Ajuste de inventario: {{ $folio_seleccionado }}">
        <x-slot name='body'>
            <!-- CONTENIDO NORMAL -->
            <div class="h-96 max-w-4xl overflow-y-auto">
                {{-- Tabla detalles del inventario --}}
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class=" px-6 py-3">
                                DESCRIPCION
                            </th>
                            <th scope="col" class=" px-6 py-3">
                                EXISTENCIA TEORICA
                            </th>
                            <th scope="col" class=" px-6 py-3">
                                EXISTENCIA REAL
                            </th>
                            <th scope="col" class=" px-6 py-3">
                                DIFERENCIA
                            </th>
                            <th scope="col" class=" px-6 py-3">
                                IMPORTE
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->inventario_detalles as $i => $detalle)
                            <tr wire:key='{{ $i }}'
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                                <td class=" px-6 py-3 ">
                                    {{ $detalle->descripcion }}
                                </td>
                                <td class=" px-6 py-3 ">
                                    {{ $detalle->stock_teorico }}
                                </td>
                                <td class=" px-6 py-3 ">
                                    {{ $detalle->stock_fisico }}
                                </td>
                                <td class=" px-6 py-3 ">
                                    {{ $detalle->diferencia_almacen }}
                                </td>
                                <td class=" px-6 py-3 ">
                                    ${{ number_format($detalle->diferencia_importe, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-slot>
    </x-modal>
</div>
