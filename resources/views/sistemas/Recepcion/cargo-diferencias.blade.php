<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('sistemas.nav')
    </x-slot>
    {{-- Titulo --}}
    <h4 class="text-2xl font-bold dark:text-white">Cargar diferencias de consumo minimo(mensual)</h4>
    {{-- Formulario --}}
    <form action="{{ route('sistemas.cargoDifConsumos') }}" method="POST">
        @csrf
        {{-- fecha --}}
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
        <button type="submit"
            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
            Cargar diferencias
        </button>
        <p>NOTA: Al seleccionar una fecha. se tomaran en cuenta los consumos del mes anterior, y en caso de existir
            diferencias se cargaran automaticamente en el mes seleccionado.
            <br>
            NO EJECUTAR DOS veces el metodo, de lo contrario se repetiran los conceptos.
        </p>
    </form>
</x-app-layout>
