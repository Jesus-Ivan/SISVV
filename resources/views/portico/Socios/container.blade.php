<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('portico.nav')
    </x-slot>
    <livewire:portico.socios.container/>
</x-app-layout>
