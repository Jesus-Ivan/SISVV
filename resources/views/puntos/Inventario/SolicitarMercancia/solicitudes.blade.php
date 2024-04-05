<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('puntos.nav')
    </x-slot>
    {{-- Contenido --}}
    <div class="p-2">
        <!--Titulo -->
        <div class="inline-flex flex-grow py-2">
            <a type="button" href="{{ route('pv.mercancia.nueva', ['codigopv'=> $codigopv]) }}"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                    <path fill-rule="evenodd"
                        d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z"
                        clip-rule="evenodd" />
                </svg>
            </a>
            <h4 class="flex items-center ms-2 text-2xl font-bold dark:text-white">Solicitar mercancia</h4>
        </div>
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
                            ORIGEN
                        </th>
                        <th scope="col" class="px-4 py-3">
                            DESTINO
                        </th>
                        <th scope="col" class="px-6 py-3">
                            OBSERVACIONES
                        </th>
                        <th scope="col" class="px-6 py-3">
                            ESTADO
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
                            12/12/2023
                        </td>
                        <td class="px-6 py-4">
                            ALMACÉN
                        </td>
                        <td class="px-6 py-4">
                            CAFETERÍA
                        </td>
                        <td class="px-6 py-4">
                            REPARTO DE MERCANCIA REALIZADO PARA EL PUNTO DE VENTA
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-2.5 w-2.5 rounded-full bg-green-500 me-2"></div> Aprobado
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex">
                                <a type="button" href="{{ route('pv.mercancia.ver', ['codigopv'=>$codigopv, 'folio'=>1234]) }}"
                                    class="text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2 text-center inline-flex items-blue me-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500">
                                    <svg class="w-5 h-5 aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd"
                                            d="M8 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1h2a2 2 0 0 1 2 2v15a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h2Zm6 1h-4v2H9a1 1 0 0 0 0 2h6a1 1 0 1 0 0-2h-1V4Zm-6 8a1 1 0 0 1 1-1h6a1 1 0 1 1 0 2H9a1 1 0 0 1-1-1Zm1 3a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2H9Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Ver Lista
                                </a>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
