<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('almacen.nav')
    </x-slot>

    {{-- Contenido --}}
    <div class="ms-3 mx-3">
        {{-- DETALLES PRODUCTOS --}}
        <h4 class="flex items-center py-2 text-2xl font-bold dark:text-white">PRODUCTOS</h4>
        <hr class="h-px my-1 bg-gray-300 border-0 dark:bg-gray-700">
        <livewire:almacen.grupos.productos />
    </div>

    {{-- Contenido --}}
    <div class="ms-3 mx-3">
        {{-- DETALLES INSUMOS --}}
        <h4 class="flex items-center py-2 text-2xl font-bold dark:text-white">INSUMOS Y PRESENTACIÓNES</h4>
        <hr class="h-px my-1 bg-gray-300 border-0 dark:bg-gray-700">
        <livewire:almacen.grupos.insumos />
    </div>
    {{-- Contenido --}}
    <div class="ms-3 mx-3">
        {{-- DETALLES MODIFICADORES --}}
        <h4 class="flex items-center py-2 text-2xl font-bold dark:text-white">MODIFICADORES</h4>
        <hr class="h-px my-1 bg-gray-300 border-0 dark:bg-gray-700">
        <livewire:almacen.grupos.modificadores />
    </div>
</x-app-layout>
