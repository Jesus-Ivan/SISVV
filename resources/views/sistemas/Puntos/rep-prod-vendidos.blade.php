<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('sistemas.nav')
    </x-slot>

    {{-- Contenido --}}
    <form action="{{ route('prod-vendidos') }}" method="POST" class="py-2 px-4">
        @csrf
        <h1 class="font-bold">REPORTE DE PRODUCTOS VENDIDOS</h1>
        <div class="flex gap-3">
            <div>
                <label for="fInicio">Fecha de inicio</label>
                <input id='fInicio' name="fInicio" type="date">
            </div>
            <div>
                <label for="fFin">Fecha de fin</label>
                <input id="fFin" name="fFin" type="date">
            </div>
            <div>
                <label for="fFin">Punto de venta</label>
                <select name="codigopv" id="codigopv">
                    <option value="ALL">TODOS</option>
                    @foreach ($puntos as $punto)
                        <option value="{{ $punto->clave }}">{{ $punto->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="my-2">
            <input type="checkbox" name="eliminados" id="eliminados">
            <label for="eliminados">Incluir eliminados</label>
        </div>
        <button type="submit"
            class="my-2 p-2 bg-slate-500 border-slate-800 border-spacing-2 text-gray-50 rounded-md">Generar
            reporte</button>
    </form>
    <form x-data="{
        legacy: false,
        maxdate: '2025-09-29',
        mindate: '2025-09-30',
        fInicio: '',
        fFin: '',
        clear() {
            this.fInicio = '';
            this.fFin = '';
        }
    }" action="{{ route('prod-vendidos-total') }}" method="POST" target="_blank"
        class="py-2 px-4">
        @csrf
        <h1 class="font-bold">REPORTE DE PRODUCTOS VENDIDOS - TOTALIZADO</h1>
        <div class="flex gap-3">
            <div>
                <label for="fInicio">Fecha de inicio</label>
                <input id='fInicio' name="fInicio" type="date" x-model="fInicio"
                    x-bind:min="legacy ? '' : mindate">
            </div>
            <div>
                <label for="fFin">Fecha de fin</label>
                <input id="fFin" name="fFin" type="date" x-model="fFin" x-bind:max="legacy ? maxdate : ''">
            </div>
        </div>
        <div class="flex">
            <div class="flex items-center h-5">
                <input x-on:click="clear()" id="helper-checkbox" aria-describedby="helper-checkbox-text" type="checkbox" x-model="legacy" name="legacy"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
            </div>
            <div class="ms-2 text-sm">
                <label x-on:click="clear()" for="helper-checkbox"
                    class="p-2 font-medium text-gray-900 dark:text-gray-300">Productos
                    Legacy</label>
                <p id="helper-checkbox-text" class="text-xs font-normal text-gray-500 dark:text-gray-300">Fecha limite
                    29-09-2025</p>
            </div>
        </div>
        <button type="submit"
            class="my-2 p-2 bg-slate-500 border-slate-800 border-spacing-2 text-gray-50 rounded-md">Generar
            reporte</button>
    </form>
</x-app-layout>
