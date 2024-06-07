<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('puntos.nav')
    </x-slot>

    {{-- Contenido --}}
    <div>
        <!-- Title -->
        <h4 class="text-2xl font-bold dark:text-white mx-2">Reporte de ventas-{{$codigopv}}</h4>
        <livewire:puntos.ventas.reporte.container :codigopv="$codigopv"/>
    </div>
</x-app-layout>