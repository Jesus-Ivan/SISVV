<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('puntos.nav')
    </x-slot>
    <!-- Title -->
    <h4 class="text-2xl font-bold dark:text-white mx-2">MI CAJA</h4>
    <livewire:caja codigopv="{{ $codigopv }}"/>
</x-app-layout>
