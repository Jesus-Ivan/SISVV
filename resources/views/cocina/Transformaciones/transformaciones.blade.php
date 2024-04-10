<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('cocina.nav')
    </x-slot>

    {{-- Contenido --}}
    <div class="py-5">
        <div class="flex ms-3">
            <button id="dropdownMenuIconButton" data-dropdown-toggle="dropdownDots"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm  px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                type="button">
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                    viewBox="0 0 16 3">
                    <path
                        d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z" />
                </svg>
            </button>
            <!-- Dropdown menu -->
            <div id="dropdownDots"
                class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 dark:divide-gray-600">
                <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownMenuIconButton">
                    <li>
                        <a href="{{ route('cocina.transformaciones.nueva') }}"
                            class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Nueva
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('cocina.transformaciones.historial') }}"
                            class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Historial
                        </a>
                    </li>
                </ul>
            </div>
            <!-- Title -->
            <h4 class="flex items-center ms-2 text-2xl font-bold dark:text-white">Transformaciones</h4>
        </div>
    </div>

    {{-- Tabla de transformaciones --}}
    <div class="ms-3 mx-3">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            FOLIO DE TRANSFORMACIÓN
                        </th>
                        <th scope="col" class="px-6 py-3 w-96">
                            INSUMO
                        </th>
                        <th scope="col" class="px-6 py-3">
                            FECHA
                        </th>
                        <th scope="col" class="text-center px-6 py-3">
                            CANTIDAD RESULTANTE
                        </th>
                        <th scope="col" class="text-center px-6 py-3">
                            PESO RESULTANTE
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                        <td class="px-6 py-4">
                            1025
                        </td>
                        <td class="px-6 py-4">
                            SUPREMAS DE POLLO
                        </td>
                        <td class="px-6 py-4">
                            10/01/2024 13:34
                        </td>
                        <td class="text-center px-6 py-4">
                            12
                        </td>
                        <td class="text-center px-6 py-4">
                            5.700 KG
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
