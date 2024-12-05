<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('almacen.nav')
    </x-slot>

    {{-- Contenido --}}
    <div class="container py-3">
        <h4 class="flex items-center ms-2 text-2xl font-bold dark:text-white">LISTA DE UNIDADES</h4>
    </div>

    <div>
        <livewire:almacen.unidades.principal />
    </div>

    {{-- Linea divisora y boton de regresar --}}
    <div class="ms-3 mx-3">
        <hr class="h-px my-2 bg-gray-300 border-0 dark:bg-gray-700">
        <a type="button" href="{{ route('almacen') }}"
            class="my-2 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
            <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 12h14M5 12l4-4m-4 4 4 4" />
            </svg>Regresar
        </a>
    </div>
</x-app-layout>
