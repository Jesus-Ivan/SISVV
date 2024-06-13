<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('sistemas.nav')
    </x-slot>

    {{-- Contenido --}}
    <form action="{{ route('sistemas.cargoMensualidades') }}" method="POST">
        @csrf
        {{-- Titulo --}}
        <h4 class="text-2xl font-bold dark:text-white">Cargar mensualidades</h4>

        {{-- Estado de membresias y date --}}
        <div class="flex">
            <div>
                <label for="estado_membresia" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipo de
                    membresias
                </label>
                <select id="estado_membresia" name="estado_membresia"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected>Choose a country</option>
                    <option value="US">United States</option>
                    <option value="CA">Canada</option>
                    <option value="FR">France</option>
                    <option value="DE">Germany</option>
                </select>
            </div>
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
            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Cargar
            mensualidades</button>
    </form>

    @if (session('success'))
        <div class="ms-3 text-sm font-medium">
            {{ session('success') }}
        </div>
    @else
        <div class="ms-3 text-sm font-medium">
            {{ session('fail') }}
        </div>
    @endif

</x-app-layout>
