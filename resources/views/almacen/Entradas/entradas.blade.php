<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('almacen.nav')
    </x-slot>

    {{-- TITTLE Y BOTONES DE ACCION --}}
    <div class="py-3">
        <div class="flex ms-3">
            <div class="inline-flex flex-grow ">
                <div>
                    <button id="dropdownMenuIconButton" data-dropdown-toggle="dropdownDots"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
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
                        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200"
                            aria-labelledby="dropdownMenuIconButton">
                            <li>
                                <a href="{{ route('almacen.entradas.nueva') }}"
                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Nueva
                                    entrada</a>
                            </li>
                            <li>
                                <a href="{{ route('almacen.entradas.historial') }}"
                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Historial entradas</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <h4 class="flex items-center ms-2 text-2xl font-bold dark:text-white">ENTRADA DE ARTICULOS</h4>
            </div>


        </div>
    </div>

    <div>
        <livewire:almacen.entradas.entrada />
    </div>

    <div class="ms-3 mx-3">
        {{-- Boton de regresar --}}
        <a type="button" href="{{ route('almacen') }}"
            class="my-2 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
            <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 12h14M5 12l4-4m-4 4 4 4" />
            </svg>Regresar
        </a>
    </div>

</x-app-layout>
