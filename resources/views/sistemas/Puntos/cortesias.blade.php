<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('sistemas.nav')
    </x-slot>
    
    {{-- Contenido --}}
    <livewire:sistemas.puntos.cortesias />

</x-app-layout>
