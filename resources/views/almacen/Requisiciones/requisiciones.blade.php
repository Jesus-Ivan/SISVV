<style>
    input[type="number"] {
        -webkit-appearance: none;
        /* Desactiva la apariencia predeterminada */
    }

    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        display: none;
        /* Oculta los botones */
    }
</style>
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
                                <a href="{{ route('almacen.requi.nueva') }}"
                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Nueva
                                    requisicion</a>
                            </li>
                            <li>
                                <a href="{{ route('almacen.requi.historial') }}"
                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Historial
                                    requisiciones</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <h4 class="flex items-center ms-2 text-2xl font-bold dark:text-white">REQUISICIONES</h4>
            </div>
        </div>
    </div>
    {{--Componente livewire--}}
    <livewire:almacen.requisiciones.principal />

</x-app-layout>
