<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('sistemas.nav')
    </x-slot>
    {{-- Titulo --}}
    <h4 class="text-2xl font-bold dark:text-white">Calcular recargos</h4>
    {{-- Contenido --}}
    <form action="{{ route('sistemas.recargos') }}" method="POST">
        @csrf
        {{-- Estado de membresias y date --}}
        <div class="flex">
            <div>
                <label for="fecha" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Fecha a cargar
                </label>
                <input type="date" id="fecha" name="fecha"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    required />
            </div>
        </div>
        {{-- Submit --}}
        <button type="submit"
            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
            Calcular recargos
        </button>

    </form>


</x-app-layout>
