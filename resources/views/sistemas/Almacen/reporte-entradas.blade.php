<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('sistemas.nav')
    </x-slot>

    {{-- Contenido --}}
    <!-- TITULO -->
    <h4 class="text-2xl font-bold dark:text-white mx-2">REPORTE DE ENTRADAS</h4>
    <form action="{{ route('sistemas.almacen.reporte-entradas') }}" method="POST" target="_blank">
        @csrf
        <div class="flex gap-4">
            {{-- FECHA DE INICIO --}}
            <div class="mb-5">
                <label for="fInicio" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fecha
                    inicio</label>
                <input type="date" id="fInicio" name="fInicio"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    required />
            </div>
            {{-- FECHA DE FIN --}}
            <div class="mb-5">
                <label for="fFin" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fecha
                    fin</label>
                <input type="date" id="fFin" name="fFin"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    required />
            </div>
            {{-- SELECT PROVEEDOR --}}
            <div>
                <label for="proveedor"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Proveedor</label>
                <select id="proveedor" name="proveedor"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="{{ null }}" selected>TODOS</option>
                    @foreach ($proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                    @endforeach
                </select>
            </div>
            {{-- BTN GENERAR --}}
            <button type="submit"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Generar
                reporte</button>

        </div>

    </form>
    {{-- Linea divisora --}}
    <hr class="h-px my-1 bg-gray-300 border-0 dark:bg-gray-700">


</x-app-layout>
