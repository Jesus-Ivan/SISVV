<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('almacen.nav')
    </x-slot>

    {{-- Contenido --}}
    <div>
        <!-- Title -->
        <h4 class="uppercase text-2xl font-bold dark:text-white mx-4">EDITAR ORDEN DE COMPRA: {{ $folio }} </h4>
        <div>
            <livewire:almacen.ordenes.editar-orden :venta="$folio" />
        </div>
    </div>
</x-app-layout>