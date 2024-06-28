<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('puntos.nav')
    </x-slot>

    {{-- Contenido --}}
    <div class="m-2 ms-3 mx-3">
        <!-- Title -->
        <h4 class="text-2xl font-bold dark:text-white my-3">Nueva venta - {{ $codigopv }}</h4>
        <livewire:puntos.ventas.nueva.container :codigopv="$codigopv" />
    </div>
</x-app-layout>
