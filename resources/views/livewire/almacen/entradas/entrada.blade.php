<div>
    {{-- Buscar por fecha --}}
    <div class="relative ms-3 w-40">
        <label for="name" class="block mb-1 text-base font-medium text-gray-900 dark:text-white">Buscar por
            día:</label>
        <input type="date" id="fecha"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
    </div>

    {{-- Tabla con información --}}
    <div class="ms-3 mx-3 ">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-2">
                            FOLIO ENTRADA
                        </th>
                        <th scope="col" class="px-6 py-2">
                            ORDEN DE COMPRA
                        </th>
                        <th scope="col" class="px-6 py-2">
                            FECHA ENTRADA
                        </th>
                        <th scope="col" class="px-6 py-2">
                            TOTAL ENTRADAS
                        </th>
                        <th scope="col" class="px-6 py-2">
                            SUBTOTAL
                        </th>
                        <th scope="col" class="px-6 py-3">
                            IVA
                        </th>
                        <th scope="col" class="px-6 py-3">
                            TOTAL
                        </th>
                        <th scope="col" class="px-6 py-3">
                            ACCIONES
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->entradas as $index => $entrada)
                        <tr wire:key='{{ $index }}'
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-2">
                                {{ $entrada->folio }}
                            </td>
                            <td class="px-6 py-2">
                                {{ $entrada->folio_orden_compra }}
                            </td>
                            <td class="px-6 py-2">
                                {{ $entrada->fecha }}
                            </td>
                            <td class="px-6 py-2">
                                hard
                            </td>
                            <td class="px-6 py-2">
                                ${{ $entrada->subtotal }}
                            </td>
                            <td class="px-6 py-2">
                                ${{ $entrada->iva }}
                            </td>
                            <td class="px-6 py-2">
                                ${{ $entrada->total }}
                            </td>
                            <td class="px-6 py-2">
                                <div class="flex">
                                    <button type="button"
                                        class="text-gray-700 hover:text-white border border-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-3.5 py-1.5 text-center me-2 mb-2 dark:border-gray-500 dark:text-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                            class="w-5 h-5">
                                            <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                                            <path fill-rule="evenodd"
                                                d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal para dar entrada articulos con el no. de folio --}}
    <x-modal name="añadirEd" title="NUEVA ENTRADA">
        <x-slot:body>
            <div class="grid gap-1 mb- grid-cols-3">
                {{-- BARRA DE BUSQUEDA --}}
                <form wire:submit='buscarOrden' class="col-span-1">
                    <div>
                        <div class="relative">
                            <label for="default-search"
                                class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3">
                                <svg wire:loading.remove wire:target='buscarOrden'
                                    class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                                <!--Loading indicator-->
                                <div wire:loading wire:target='buscarOrden'>
                                    @include('livewire.utils.loading', ['w' => 5, 'h' => 5])
                                </div>
                            </div>
                            <input wire:model="folio_search" type="text"
                                class="block w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Buscar folio" required />
                        </div>
                        <button type="submit" class="w-0 h-0" />
                    </div>
                </form>
                {{-- DETALLES ORDEN (TABLA) --}}
                <div class="col-span-3">
                    <div class="relative overflow-y-auto h-64">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="max-h-10 px-3 py-2">
                                        CÓDIGO
                                    </th>
                                    <th scope="col" class="max-h-36 px-6 py-2">
                                        DESCRIPCIÓN
                                    </th>
                                    <th scope="col" class="px-6 py-2">
                                        UNIDAD
                                    </th>
                                    <th scope="col" class="px-6 py-2">
                                        CANTIDAD
                                    </th>
                                    <th scope="col" class="px-6 py-2">
                                        COSTO UNIT.
                                    </th>
                                    <th scope="col" class="px-6 py-2">
                                        IMPORTE
                                    </th>
                                    <th scope="col" class="px-6 py-2">
                                        IVA
                                    </th>
                                    <th scope="col" class="px-6 py-2">
                                        FECHA COMPRA
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orden_result as $index => $row)
                                    <tr wire:key='{{ $index }}'
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <th class="px-3 py-2 text-center">
                                            {{ $row->codigo_producto }}
                                        </th>
                                        <td class="px-6 py-2 uppercase">
                                            {{ $row->nombre }}
                                        </td>
                                        <td class="px-6 py-2 uppercase">
                                            {{ $row->id_unidad }}
                                        </td>
                                        <td class="px-6 py-2">
                                            {{ $row->cantidad }}
                                        </td>
                                        <td class="px-6 py-2">
                                            ${{ $row->costo_unitario }}
                                        </td>
                                        <td class="px-6 py-2">
                                            ${{ $row->importe }}
                                        </td>
                                        <td class="px-6 py-2">
                                            ${{ $row->iva }}
                                        </td>
                                        <td class="px-6 py-2">
                                            <input type="date" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    {{-- DETALLES DE IMPORTE DE LA ORDEN DE COMPRA --}}
                    <label for="name"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Importe</label>
                    <input type="text" id="disabled-input" aria-label="disabled input"
                        class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-fit p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        value="Importe" disabled>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    {{-- DETALLES DE IVA DE LA ORDEN DE COMPRA --}}
                    <label for="name"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Iva</label>
                    <input type="text" id="disabled-input" aria-label="disabled input"
                        class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-fit p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        value="Iva" disabled>
                </div>
                <div class="col-span-2 sm:col-span-1 ">
                    {{-- DETALLES DEL TOTAL DE LA ORDEN DE COMPRA --}}
                    <label for="name"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Total</label>
                    <input type="text" aria-label="disabled input"
                        class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-fit p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        value="Total" disabled>
                </div>
            </div>
        </x-slot>
        <x-slot:footer>
            <button type="button"
                class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Aplicar
                Entrada
            </button>
            <button x-on:click="show = false" type="button"
                class="py-2.5 px-5 ms-3 text-sm font-medium text-red-900 focus:outline-none bg-red rounded-lg border border-red-200 hover:bg-red-100 hover:text-red-700 focus:z-10 focus:ring-4 focus:ring-red-100 dark:focus:ring-red-700 dark:bg-red-800 dark:text-red-400 dark:border-red-600 dark:hover:text-white dark:hover:bg-red-700">Cancelar
            </button>
        </x-slot>
    </x-modal>
</div>
