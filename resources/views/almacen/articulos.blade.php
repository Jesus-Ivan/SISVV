<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('almacen.nav')
    </x-slot>

    {{-- Contenido --}}
    <div class="container py-5">
        <div class="flex ms-3">
            <button x-data x-on:click="$dispatch('open-modal', { name: 'añadir' })"
                class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                type="button">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </button>
            <h4 class="flex items-center ms-2 text-2xl font-bold dark:text-white">Lista de productos</h4>
        </div>
    </div>

    <div>
        <livewire:almacen.articulos />
    </div>

    <div class="ms-3 mx-3">
        {{-- Linea divisora --}}
        <hr class="h-px my-1 bg-gray-300 border-0 dark:bg-gray-700">

        {{-- Boton de regresar --}}
        <a type="button" href="{{ route('almacen') }}"
            class="my-2 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
            <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 12h14M5 12l4-4m-4 4 4 4" />
            </svg>Regresar
        </a>

        {{-- Ayuda visual para los indicadores --}}
        <div class="max-w-lg p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
            <h2 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">INDICADORES</h2>
            <ul class="max-w-xl space-y-1 text-gray-500 list-inside dark:text-gray-400">
                <li>
                    <span
                        class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Verde
                    </span>
                    Stock adecuado o artículo activo.
                </li>
                <li>
                    <span
                        class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Rojo
                    </span>
                    Stock vacío o artículo inactivo.
                </li>
                <li>
                    <span
                        class="bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">Amarillo
                    </span>
                    Sugerencia para compra o surtido a puntos de venta.
                </li>
            </ul>
        </div>
    </div>
</x-app-layout>