<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('almacen.nav')
    </x-slot>

    {{-- Contenido --}}
    <div class="container py-5">
        <div class="flex ms-3">
            <div class="inline-flex flex-grow">
                <h4 class="flex items-center ms-2 text-2xl font-bold dark:text-white">Historial de entradas</h4>
            </div>
        </div>
    </div>

    <div class="ms-3">
        <!--Fecha-->
        <div class="relative w-fit my-1">
            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                </svg>
            </div>
            <input datepicker type="text" datepicker-format="dd/mm/yyyy"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Seleccionar fecha">
        </div>
    </div>

    {{-- Tabla --}}
    <div class="ms-3 mx-3">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <div
                class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4">
                <label for="table-search" class="sr-only">Buscar</label>
                <div class="relative">
                    <div
                        class="absolute inset-y-0 left-0 rtl:inset-r-0 rtl:right-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor"
                            viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <input type="text" id="table-search"
                        class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Buscar">
                </div>
            </div>
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            FECHA
                        </th>
                        <th scope="col" class="px-6 py-3">
                            FOLIO DE ENTRADA
                        </th>
                        <th scope="col" class="px-6 py-3">
                            CÓDIGO
                        </th>
                        <th scope="col" class="px-4 py-3">
                            DESCRIPCIÓN
                        </th>
                        <th scope="col" class="px-4 py-3">
                            CANTIDAD
                        </th>
                        <th scope="col" class="px-4 py-3">
                            COSTO UNITARIO
                        </th>
                        <th scope="col" class="px-4 py-3">
                            TOTAL
                        </th>
                        <th scope="col" class="px-6 py-3">
                            IVA
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4">
                            12/12/2023
                        </td>
                        <td class="px-6 py-4">
                            1234
                        </td>
                        <td class="px-6 py-4">
                            5050
                        </td>
                        <td class="px-6 py-4">
                            COCA COLA LATA 355ML
                        </td>
                        <td class="px-6 py-4">
                            3
                        </td>
                        <td class="px-6 py-4">
                            $20.00
                        </td>
                        <td class="px-6 py-4">
                            $60.00
                        </td>
                        <td class="px-6 py-4">
                            $10.00
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</x-app-layout>