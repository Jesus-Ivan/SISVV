<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('sistemas.nav')
    </x-slot>

    {{-- REPORTE DE VENTAS - PAGOS --}}
    <div class="p-2">
        <div class="flex items-center">
            <!-- TITULO -->
            <h4 class="text-2xl font-bold dark:text-white">Reporte de ventas - pagos</h4>
        </div>
        <form action="{{ route('sistemas.reportes.ventas') }}" method="POST" target="_blank">
            @csrf
            <div class="flex gap-4 items-end">
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
                <input class="h-fit" type="date" id="fechaInicio" name="fechaInicio">
                <input class="h-fit" type="date" id="fechaFin" name="fechaFin">
                <x-primary-button class="h-11" type="submit">Generar</x-primary-button>
            </div>
        </form>
        {{-- Linea divisora --}}
        <hr class="h-px my-1 bg-gray-300 border-0 dark:bg-gray-700">
    </div>
    {{-- REPORTE DE RECIBOS - DETALLES --}}
    <div class="p-2">
        <div class="flex items-center">
            <!-- TITULO -->
            <h4 class="text-2xl font-bold dark:text-white ">Reporte mensual de recibos - detalles</h4>
        </div>
        <form action="{{ route('sistemas.reportes.recibos') }}" method="POST" target="_blank">
            @csrf
            <input type="date" id="fechaInicio" name="fechaInicio">
            <input type="date" id="fechaFin" name="fechaFin">
            <x-primary-button class="h-11" type="submit">Generar</x-primary-button>
        </form>
        {{-- Linea divisora --}}
        <hr class="h-px my-1 bg-gray-300 border-0 dark:bg-gray-700">
    </div>
    {{-- REPORTE DE SOCIOS --}}
    <div class="p-2">
        <div class="flex items-center ">
            <!-- TITULO -->
            <h4 class="text-2xl font-bold dark:text-white ">Reporte mensual de SOCIOS</h4>
        </div>
        <form action="{{ route('sistemas.reportes.socios') }}" method="POST" target="_blank">
            <p>Este reporte muestra los socios actualizados, al momento de descargarlo.</p>
            @csrf
            <x-primary-button class="h-11" type="submit">Generar</x-primary-button>
        </form>
        {{-- Linea divisora --}}
        <hr class="h-px my-1 bg-gray-300 border-0 dark:bg-gray-700">
    </div>
    {{-- REPORTE FIRMAS --}}
    <div class="p-2">
        <form action="{{ route('sistemas.reportes.firmas') }}" method="POST" target="__blank">
            <div class="flex items-center">
                <!-- TITULO -->
                <h4 class="text-2xl font-bold dark:text-white">Reporte de Firmas x pagar</h4>
            </div>
            <p class="my-2">Genera reporte con todas las firmas pendientes de pagar hasta la fecha indicada</p>
            <p>NOTA: Por defecto aplica la exclusion de notas no vencidas del mes anterior; Segun los primeros 10 dias de cada fecha.</p>
            <div class="flex my-2">
                <div class="flex items-center h-5">
                    <input id="notasLimite" aria-describedby="notasLimite-text" type="checkbox" name="notasLimite"
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                </div>
                <div class="ms-2 text-sm">
                    <label for="notasLimite" class="font-medium text-gray-900 dark:text-gray-300">Incluir notas
                        limite</label>
                    <p id="notasLimite-text" class="text-xs font-normal text-gray-500 dark:text-gray-300">Incluye
                        todas las notas hasta la fecha seleccionada (no vencidas)</p>
                </div>
            </div>
            <input class="h-fit" type="date" id="fechaFin" name="fechaFin">
            @csrf
            <x-primary-button class="h-11 mx-2" type="submit">Generar</x-primary-button>
        </form>
        {{-- Linea divisora --}}
        <hr class="h-px my-1 bg-gray-300 border-0 dark:bg-gray-700">
    </div>
</x-app-layout>
