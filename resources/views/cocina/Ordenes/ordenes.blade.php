<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('cocina.nav')
    </x-slot>
    {{-- Contenido --}}
    <div class="m-2">
        <!-- Title -->
        <h4 class=" text-2xl font-bold dark:text-white">Ordenes del dia</h4>
        <livewire:cocina.ordenes />
    </div>
</x-app-layout>
