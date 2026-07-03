<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('almacen.nav')
    </x-slot>

    {{-- Contenido --}}
    <div>
        <livewire:almacen.traspasos.v2.nuevo-traspaso :folio_pedido="$folio_pedido" />
    </div>
</x-app-layout>
