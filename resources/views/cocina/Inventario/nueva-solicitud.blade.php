<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('cocina.nav')
    </x-slot>

    {{-- Contenido --}}
    <div>
        <livewire:cocina.inventario.nueva-solicitud />
    </div>

</x-app-layout>