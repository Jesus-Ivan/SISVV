<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('sistemas.nav')
    </x-slot>
    {{-- Titulo --}}
    <h4 class="text-2xl font-bold dark:text-white">Cargar Anualidad</h4>
    <livewire:sistemas.recepcion.anualidad.nueva>
</x-app-layout>
