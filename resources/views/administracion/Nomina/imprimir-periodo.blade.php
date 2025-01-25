<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('administracion.nav')
    </x-slot>
    <!-- Title -->
    <h4 class="text-2xl font-bold dark:text-white my-2 mx-4">IMPRIMIR PERIODO DE NOMINA</h4>

    {{-- Contenido --}}
    <div class="mx-4">
        {{-- Search form --}}
        <form class="max-w-md " method="GET" action="{{ route('administracion.buscar-p') }}">
            <label for="default-search"
                class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
            <div class="relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                    </svg>
                </div>
                <input type="text" name="year"
                    class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Año de nomina" required />
                <button type="submit"
                    class="text-white absolute end-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Search</button>
            </div>
        </form>

        {{-- Table result --}}
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            REFERENCIA
                        </th>
                        <th scope="col" class="px-6 py-3">
                            USUARIO
                        </th>
                        <th scope="col" class="px-6 py-3">
                            INICIO PERIODO
                        </th>
                        <th scope="col" class="px-6 py-3">
                            FIN PERIODO
                        </th>
                        <th scope="col" class="px-6 py-3">
                            FECHA
                        </th>
                        <th scope="col" class="px-6 py-3">
                            ACCIONES
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($periodos as $index => $periodo)
                        <tr wire:key='{{ $index }}'
                            class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                            <th scope="row"
                                class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $periodo->referencia }}
                            </th>
                            <td class="px-6 py-2">
                                {{ $periodo->nombre }}
                            </td>
                            <td class="px-6 py-2">
                                {{ $periodo->fecha_inicio }}
                            </td>
                            <td class="px-6 py-2">
                                {{ $periodo->fecha_fin }}
                            </td>
                            <td class="px-6 py-2">
                                {{ $periodo->created_at }}
                            </td>
                            <td class="px-6 py-2">
                                <a href="{{ route('administracion.imprimir-p', ['ref' => $periodo->referencia]) }}" target="_blank"
                                    class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Imprimir</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div>
            {{ $periodos->links() }}
        </div>

    </div>

</x-app-layout>
