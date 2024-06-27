<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('puntos.nav')
    </x-slot>
    <livewire:caja codigopv="{{ $codigopv }}"/>
</x-app-layout>
