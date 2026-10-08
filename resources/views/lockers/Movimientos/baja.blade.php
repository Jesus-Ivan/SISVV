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
        @include('lockers.nav')
    </x-slot>

    {{-- Contenido --}}
    <div>
        <h4 class="text-2xl font-bold dark:text-white mx-2">
            BAJA LOCKERS
            <button data-popover-target="popover-description" data-popover-placement="bottom-end" type="button"><svg
                    class="w-5 h-5 ms-2 text-gray-400 hover:text-gray-500" aria-hidden="true" fill="currentColor"
                    viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                        clip-rule="evenodd"></path>
                </svg><span class="sr-only">Show information</span></button>
        </h4>
        <div data-popover id="popover-description" role="tooltip"
            class="absolute z-10 invisible inline-block text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-xs opacity-0 w-72 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400">
            <div class="p-3 space-y-2">
                <h3 class="font-semibold text-gray-900 dark:text-white">Locker Extraordinarios</h3>
                <p>En este modulo unicamente se puede realizar la Baja de un locker extraordinario (locker sin numero
                    de socio)</p>
            </div>
            <div data-popper-arrow></div>
        </div>
    </div>
    {{-- Contenido --}}
    <livewire:lockers.movimientos.baja />
</x-app-layout>
