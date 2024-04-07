<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('administracion.nav')
    </x-slot>

    {{-- Contenido --}}
    <div class="py-5">
        <div class="flex ms-3">
            <div class="inline-flex flex-grow">
                <h4 class="flex items-center ms-2 text-2xl font-bold dark:text-white">Detalles de Orden de compra - 1234
                </h4>
            </div>

            <div class="inline-flex">
                <label for="name" class="flex items-center text-sm font-medium text-gray-900 dark:text-white">FECHA:
                </label>
                <input type="text" id="disabled-input" aria-label="disabled input"
                    class="ms-2 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    value="Fecha" disabled>
            </div>
            <div class="inline-flex">
                <label for="name"
                    class="flex items-center ms-2 text-sm font-medium text-gray-900 dark:text-white">FOLIO:
                </label>
                <input type="text" id="disabled-input" aria-label="disabled input"
                    class="ms-2 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed me-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    value="Fecha" disabled>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="ms-3 mx-3">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">

                        </th>
                        <th scope="col" class="px-6 py-3">
                            CÓDIGO
                        </th>
                        <th scope="col" class="px-6 py-3">
                            DESCRIPCIÓN
                        </th>
                        <th scope="col" class="px-6 py-3">
                            CANTIDAD
                        </th>
                        <th scope="col" class="px-4 py-3">
                            UNIDAD
                        </th>
                        <th scope="col" class="px-4 py-3">
                            PROVEEDOR
                        </th>
                        <th scope="col" class="px-4 py-3">
                            COSTO UNITARIO
                        </th>
                        <th scope="col" class="px-4 py-3">
                            IMPORTE
                        </th>
                        <th scope="col" class="px-4 py-3">
                            IVA
                        </th>
                        <th scope="col" class="px-4 py-3">
                            ALMACÉN
                        </th>
                        <th scope="col" class="px-4 py-3">
                            BAR
                        </th>
                        <th scope="col" class="px-4 py-3">
                            BARRA
                        </th>
                        <th scope="col" class="px-4 py-3">
                            CADDIE
                        </th>
                        <th scope="col" class="px-4 py-3">
                            CAFETERÍA
                        </th>
                        <th scope="col" class="px-4 py-3">
                            COCINA
                        </th>
                        <th scope="col" class="px-6 py-3">
                            ULTIMA COMPRA
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="w-4 p-4">
                            <div class="flex items-center">
                                <input id="checkbox-table-1" type="checkbox"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="checkbox-table-1" class="sr-only">checkbox</label>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            1234
                        </td>
                        <td class="px-6 py-4">
                            AGUA CIEL 600 ML
                        </td>
                        <td class="px-6 py-4">
                            5
                        </td>
                        <td class="px-6 py-4">
                            PIEZA
                        </td>
                        <td class="px-6 py-4">
                            COCA COLA
                        </td>
                        <td class="px-6 py-4">
                            $20.00
                        </td>
                        <td class="px-6 py-4">
                            $40.00
                        </td>
                        <td class="px-6 py-4">
                            $6.4
                        </td>
                        <td class="px-4 py-2">
                            <span
                                class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-green-900 dark:text-green-300">10
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            <span
                                class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-green-900 dark:text-green-300">10
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            <span
                                class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-green-900 dark:text-green-300">10
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            <span
                                class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-green-900 dark:text-green-300">10
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            <span
                                class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-green-900 dark:text-green-300">10
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            <span
                                class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-green-900 dark:text-green-300">10
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            20/05/2023
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <hr class="h-px my-4 bg-gray-300 border-0 dark:bg-gray-700">

        {{-- Botones de accion --}}
        <div class="inline-flex flex-grow mt-2">
            <a type="button" href="{{ route('administracion.reportes-ordenes') }}"
                class="text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                    height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 12h14M5 12l4-4m-4 4 4 4" />
                </svg>Regresar
            </a>
            <button type="button"
                class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                <svg class="w-5 h-5 me-2" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                    viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18 17.94 6M18 18 6.06 6" />
                </svg>
                Cancelar Orden
            </button>
            <button type="button"
                class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                <svg class="w-5 h-5 me-2" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                    viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 11.917 9.724 16.5 19 7.5" />
                </svg>Aprobar Orden
            </button>
        </div>
    </div>
</x-app-layout>
