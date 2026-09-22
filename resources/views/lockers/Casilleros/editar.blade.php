<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('lockers.nav')
    </x-slot>

    {{-- Contenido --}}
    <livewire:lockers.editar-casillero :idLocker="$id_locker" />

</x-app-layout>
