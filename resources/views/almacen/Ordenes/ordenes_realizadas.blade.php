<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('almacen.nav')
    </x-slot>

    <livewire:almacen.ordenes.revisar-ordenes />
</x-app-layout>
