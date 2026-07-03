<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('almacen.nav')
    </x-slot>

    {{-- Contenido --}}
    <div>
        <!-- Title -->
        <h4 class="flex items-center ms-3 my-2 text-2xl font-bold dark:text-white">SOLICITUDES DE MERCANCIA POR PUNTO</h4>
        <livewire:almacen.pedidos.principal />
    </div>
</x-app-layout>
