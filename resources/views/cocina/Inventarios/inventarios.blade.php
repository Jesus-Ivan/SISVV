<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('cocina.nav')
    </x-slot>
    {{-- Contenido --}}
    <div>
        <livewire:cocina.inventarios/>
    </div>
</x-app-layout>
