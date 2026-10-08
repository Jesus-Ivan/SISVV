<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('lockers.nav')
    </x-slot>

    {{-- Contenido --}}
    <livewire:lockers.casilleros />

</x-app-layout>
