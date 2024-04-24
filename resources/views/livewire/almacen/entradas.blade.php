<div>
    {{-- Buscar por fecha --}}
    <div class="relative ms-3 w-40">
        <label for="name" class="block mb-2 text-base font-medium text-gray-900 dark:text-white">Buscar por día:</label>
        <input type="date" id="fecha"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
    </div>

    {{-- Tabla con información --}}
    <div class="ms-3 mx-3 my-3">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            FOLIO
                        </th>
                        <th scope="col" class="px-6 py-3">
                            FOLIO ORDEN DE COMPRA
                        </th>
                        <th scope="col" class="px-6 py-3">
                            FECHA
                        </th>
                        <th scope="col" class="px-6 py-3">
                            TOTAL ENTRADAS
                        </th>
                        <th scope="col" class="px-6 py-3">
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
                    <tr
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4">
                            1234
                        </td>
                        <td class="px-6 py-4">
                            4321
                        </td>
                        <td class="px-6 py-4">
                            12/12/2023
                        </td>
                        <td class="px-6 py-4">
                            50 ARTÍCULOS
                        </td>
                        <td class="px-6 py-4">
                            $4,200.00
                        </td>
                        <td class="px-6 py-4">
                            $672.00
                        </td>
                        <td class="px-6 py-4">
                            $4872.00
                        </td>
                        <td class="px-6 py-4">
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
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal para dar entrada articulos con el no. de folio --}}
    <x-modal name="añadirEd" title="NUEVA ENTRADA">
        <x-slot:body>
            <form class="p-0 md:p-0">
                <div class="grid gap-1 mb- grid-cols-3">
                    <div class="col-span-1">
                        {{-- BARRA DE BUSQUEDA --}}
                        <label for="name"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Buscar folio de orden
                            de compra</label>
                        <div class="relative">
                            <label for="default-search"
                                class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input wire:model.live.debounce.500ms="search" type="search" id="search"
                                class="block w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Buscar folio" required />
                        </div>
                    </div>
                    <div class="col-span-3">
                        <label for="name"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Detalles </label>
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-3 py-3">
                                            CÓDIGO
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            DESCRIPCIÓN
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            UNIDAD
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            CANTIDAD
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            COSTO UNITARIO
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            IMPORTE
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            IVA
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <th class="px-3 py-4 text-center">
                                            2
                                        </th>
                                        <td class="px-6 py-4 uppercase">
                                            fg
                                        </td>
                                        <td class="px-6 py-4 uppercase">

                                        </td>
                                        <td class="px-6 py-4">

                                        </td>
                                        <td class="px-6 py-4">

                                        </td>
                                        <td class="px-6 py-4">

                                        </td>
                                        <td class="px-6 py-4">

                                        </td>
                                    </tr>
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
                        <input type="text" id="disabled-input" aria-label="disabled input"
                            class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-fit p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            value="Total" disabled>
                    </div>
                </div>
            </form>
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
