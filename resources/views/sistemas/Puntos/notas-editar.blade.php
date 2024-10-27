<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('sistemas.nav')
    </x-slot>
    {{-- TITULO --}}
    <h4 class="text-2xl font-bold dark:text-white my-3">EDITAR VENTA - {{ $folioVenta }}</h4>
    {{-- Contenido --}}
    <livewire:sistemas.puntos.notas-editar folio="{{ $folioVenta }}" />


</x-app-layout>
