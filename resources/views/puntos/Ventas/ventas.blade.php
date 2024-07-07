<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('puntos.nav')
    </x-slot>
    {{-- Contenido --}}
    <div class="flex items-center m-2">
        @if ($permisospv->clave_rol == 'MES')
            <!-- Nueva venta - mesero -->
            <div>
                <a type="button" href="{{ route('pv.ventas.nueva', ['codigopv' => $codigopv]) }}"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 12h14m-7 7V5" />
                    </svg>
                </a>
            </div>
        @else
            <!-- Boton de acciones cajero-->
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
                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownMenuIconButton">
                        <li>
                            <a href="{{ route('pv.ventas.nueva', ['codigopv' => $codigopv]) }}"
                                class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Nueva</a>
                        </li>
                        <li>
                            <a href="{{ route('pv.ventas.reporte', ['codigopv' => $codigopv]) }}"
                                class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Reportes</a>
                        </li>
                    </ul>
                </div>
            </div>
        @endif
        <!-- Title -->
        <h4 class="text-2xl font-bold dark:text-white mx-2">VENTAS - {{ $nombrepv }}</h4>
    </div>

    @if ($permisospv->clave_rol == 'MES')
        <livewire:puntos.ventas.principal-mesero :codigopv="$codigopv" />
    @else
        <livewire:puntos.ventas.principal :codigopv="$codigopv" />
    @endif
</x-app-layout>
