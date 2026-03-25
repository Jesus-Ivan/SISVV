<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('puntos.nav')
    </x-slot>
    <!-- Title -->
    <h4 class="text-2xl font-bold dark:text-white mx-2">COMANDAS COCINA</h4>

    <livewire:puntos.comandas.principal />
</x-app-layout>
