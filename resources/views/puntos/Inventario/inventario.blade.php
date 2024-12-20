<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('puntos.nav')
    </x-slot>

    {{-- Contenido --}}
    <div>
        <!-- Title -->
        <h4 class="text-2xl font-bold dark:text-white m-3">INVENTARIO - {{ $codigopv }}</h4>
        <livewire:puntos.inventario.ver-inventario codigopv="{{ $codigopv }}" />
    </div>
</x-app-layout>
