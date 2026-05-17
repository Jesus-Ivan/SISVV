<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('administracion.nav')
    </x-slot>
    <!-- Title -->
    <h4 class="text-2xl font-bold dark:text-white m-3">DETALLES DE GASTOS - {{ $folio }}</h4>
    {{-- Contenido --}}
    <div>
        <livewire:administracion.comprobaciones.detalle-comprobaciones :folio="$folio" />
    </div>
</x-app-layout>