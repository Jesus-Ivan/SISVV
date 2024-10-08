<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('sistemas.nav')
    </x-slot>

    {{-- REPORTE DE VENTAS - PAGOS --}}
    <div>
        <div class="flex items-center m-2">
            <!-- TITULO -->
            <h4 class="text-2xl font-bold dark:text-white mx-2">Reporte de ventas - pagos</h4>
        </div>
        <form action="{{ route('sistemas.reportes.ventas') }}" method="POST" target="_blank">
            @csrf
            <div class="flex gap-4">
                <div class="max-w-sm">
                    <label for="type_file"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Seleccionar
                        tipo de archivo</label>
                    <select id="type_file" name="type_file"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-fit p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="PDF" selectd>PDF</option>
                        <option value="XLS">EXCEL</option>
                    </select>
                </div>
            </div>
            <input type="date" id="fechaInicio" name="fechaInicio">
            <input type="date" id="fechaFin" name="fechaFin">
            <button type="submit">Generar</button>
        </form>
        <div class="ms-3 mx-3 my-2">
            {{-- Linea divisora --}}
            <hr class="h-px my-1 bg-gray-300 border-0 dark:bg-gray-700">

        </div>
    </div>
    {{-- REPORTE DE RECIBOS - DETALLES --}}
    <div>
        <div class="flex items-center m-2">
            <!-- TITULO -->
            <h4 class="text-2xl font-bold dark:text-white mx-2">Reporte mensual de recibos - detalles</h4>
        </div>
        <form action="{{ route('sistemas.reportes.recibos') }}" method="POST" target="_blank">
            @csrf
            <input type="date" id="fechaInicio" name="fechaInicio">
            <input type="date" id="fechaFin" name="fechaFin">
            <button type="submit">Generar</button>
        </form>
    </div>
    {{-- REPORTE DE SOCIOS --}}
    <div>
        <div class="flex items-center m-2">
            <!-- TITULO -->
            <h4 class="text-2xl font-bold dark:text-white mx-2">Reporte mensual de SOCIOS</h4>
        </div>
        <form action="{{ route('sistemas.reportes.socios') }}" method="POST" target="_blank">
            @csrf
            <button type="submit" class="bg-slate-600 p-2 text-white rounded-md">Generar</button>
        </form>
    </div>
    {{-- Boton de regresar --}}
    <a type="button" href="{{ route('sistemas.reportes') }}"
        class="my-2 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
        <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
            fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M5 12h14M5 12l4-4m-4 4 4 4" />
        </svg>Regresar
    </a>
</x-app-layout>
