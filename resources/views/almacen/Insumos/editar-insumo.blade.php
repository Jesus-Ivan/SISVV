<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('almacen.nav')
    </x-slot>

    {{-- Contenido --}}
    <div class="container py-2">
        <div class="flex">
            <h4 class="flex items-center ms-4 text-2xl font-bold dark:text-white">EDITAR INSUMO - {{ $clave }}
            </h4>
        </div>
    </div>

    <div>
        <livewire:almacen.insumos.editar-insumo clave="{{ $clave }}" />
    </div>

</x-app-layout>
