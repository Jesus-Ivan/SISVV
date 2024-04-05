<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('puntos.nav')
    </x-slot>
    {{-- Contenido --}}
    <div class="py-5">
        <div class="flex ms-3">
            <!--TITULO-->
            <div class="inline-flex flex-grow">
                <h4 class="flex items-center text-2xl font-bold dark:text-white">Ver solicitud de mercancia
                </h4>
            </div>
            <!--Fecha-->
            <div class="inline-flex">
                <label for="name" class="flex items-center text-sm font-medium text-gray-900 dark:text-white">FECHA:
                </label>
                <input type="text" id="disabled-input" aria-label="disabled input"
                    class="ms-2 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    value="Fecha" disabled>
            </div>
            <!--Folio-->
            <div class="inline-flex">
                <label for="name"
                    class="flex items-center ms-2 text-sm font-medium text-gray-900 dark:text-white">FOLIO:
                </label>
                <input type="text" id="disabled-input" aria-label="disabled input"
                    class="ms-2 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed me-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    value="{{$folio}}" disabled>
            </div>
        </div>
    </div>

    <div class="container py-5">
        <div class="flex ms-3">
            <div class="inline-flex flex-grow">
                <!--Salida-->
                <div class="inline-flex">
                    <label for="name"
                        class="flex items-center text-sm font-medium text-gray-900 dark:text-white">SALIDA:
                    </label>
                    <input type="text" id="disabled-input" aria-label="disabled input"
                        class="ms-2 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        value="Salida De" disabled>
                </div>
                <!--Destino-->
                <div class="inline-flex">
                    <label for="name"
                        class="ms-2 flex items-center text-sm font-medium text-gray-900 dark:text-white">DESTINO:
                    </label>
                    <input type="text" id="disabled-input" aria-label="disabled input"
                        class="ms-2 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        value="{{$codigopv}}" disabled>
                </div>
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
                            CÓDIGO
                        </th>
                        <th scope="col" class="px-6 py-3">
                            DESCRIPCIÓN
                        </th>
                        <th scope="col" class="px-6 py-3">
                            CANTIDAD
                        </th>
                        <th scope="col" class="px-4 py-3">
                            EXISTENCIAS ORIGEN
                        </th>
                        <th scope="col" class="px-4 py-3">
                            EXISTENCIAS DESTINO
                        </th>
                        <th scope="col" class="px-4 py-3">
                            ESTADO
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
                            AGUA CIEL 600 ML
                        </td>
                        <td class="px-6 py-4">
                            5
                        </td>
                        <td class="px-6 py-4">
                            100
                        </td>
                        <td class="px-6 py-4">
                            100
                        </td>
                        <td class="px-4 py-2">
                            <span
                                class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Entregado
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
