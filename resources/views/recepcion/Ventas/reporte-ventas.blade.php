<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('recepcion.nav')
    </x-slot>

    {{-- Contenido --}}
    <div>
        <!-- Title -->
        <h4 class="text-2xl font-bold dark:text-white mx-2">Reporte de ventas-Recepcion</h4>
        <livewire:recepcion.ventas-reporte codigopv="recepcion"/>
    </div>
</x-app-layout>