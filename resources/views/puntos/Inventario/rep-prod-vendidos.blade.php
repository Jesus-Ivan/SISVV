<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('puntos.nav')
    </x-slot>
    {{-- Contenido --}}
    <form class="p-4" action="{{ route('prod-vendidos') }}" method="POST">
        @csrf
        <h1 class="font-bold text-lg">REPORTE DE PRODUCTOS VENDIDOS</h1>
        <div class="mt-4 flex gap-3">
            <div class="flex flex-col">
                <label for="fInicio">Fecha de inicio</label>
                <input id='fInicio' name="fInicio" type="date">
            </div>
            <div class="flex flex-col">
                <label for="fFin">Fecha de fin</label>
                <input id="fFin" name="fFin" type="date">
            </div>
            <div class="flex flex-col">
                <label for="fFin">Punto de venta</label>
                <select name="codigopv" id="codigopv" class="pointer-events-none opacity-50">
                    <option value="ALL">TODOS</option>
                    @foreach ($puntos as $punto)
                        <option @selected($punto->clave == $codigopv) value="{{ $punto->clave }}">{{ $punto->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="py-4">
            <x-primary-button type="submit">Generar reporte</x-primary-button>
        </div>
    </form>
</x-app-layout>
