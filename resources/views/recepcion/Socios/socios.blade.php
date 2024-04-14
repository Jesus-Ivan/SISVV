<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('recepcion.nav')
    </x-slot>

    {{-- Contenido --}}
    <div class="flex items-center m-2">
        <!-- AñADIR SOCIOS -->
        <a href="{{ route('recepcion.socios.nuevo') }}">
            <button
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 12h14m-7 7V5" />
                </svg>
                <span class="sr-only">Nuevo socio</span>
            </button>
        </a>
        <!-- TITULO -->
        <h4 class="text-2xl font-bold dark:text-white mx-2">Socios</h4>
    </div>
    <livewire:recepcion.socios />
</x-app-layout>
