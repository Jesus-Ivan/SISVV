<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('almacen.nav')
    </x-slot>

    {{-- Contenido --}}
    <div class="container py-2">
        <h4 class="flex items-center ms-2 text-2xl font-bold dark:text-white">EDITAR PRODUCTO - {{ $clave }}</h4>
    </div>
    <livewire:almacen.productos.editar-producto clave="{{ $clave }}" />
</x-app-layout>