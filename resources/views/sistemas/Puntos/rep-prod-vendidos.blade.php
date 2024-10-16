<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('sistemas.nav')
    </x-slot>

    {{-- Contenido --}}
    <form action="{{ route('prod-vendidos') }}" method="POST">
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
        
        <button type="submit">Generar reporte</button>
    </form>
</x-app-layout>
