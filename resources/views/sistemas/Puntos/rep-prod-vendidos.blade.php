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
</x-app-layout>
