<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('almacen.nav')
    </x-slot>

    {{-- Contenido --}}
    <div>
        <livewire:almacen.traspasos.v2.nuevo-traspaso />
    </div>
</x-app-layout>
