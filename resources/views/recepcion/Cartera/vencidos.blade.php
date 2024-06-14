<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('recepcion.nav')
    </x-slot>

    {{-- Contenido --}}
    <form action="{{ route('recepcion.cartera.vencidos') }}" method="POST" target="_blank">
        @csrf
        <!-- Title -->
        <h4 class="text-2xl font-bold dark:text-white mx-2">Cartera de clientes vencidos</h4>
        <div class="flex gap-2 items-end">
            {{-- Fecha Inicio --}}
            <div class="w-72">
                <label for="inicio" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Inicio</label>
                <input type="date" name="fInicio" 
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            </div>
            {{-- Fecha fin --}}
            <div class="w-72">
                <label for="fin" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fin</label>
                <input type="date" name="fFin" 
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            </div>
            <!--Boton de busqueda -->
            <button type="submit"
                class="w-32 h-11 justify-center text-center inline-flex items-center text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">
                <div wire:loading.delay wire:target='buscar' class="me-4">
                    @include('livewire.utils.loading', ['w' => 5, 'h' => 5])
                </div>
                Buscar
            </button>
        </div>
    </form>
</x-app-layout>
