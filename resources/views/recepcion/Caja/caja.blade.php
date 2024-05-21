<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('recepcion.nav')
    </x-slot>

    {{-- Contenido --}}
    <livewire:caja />
</x-app-layout>
