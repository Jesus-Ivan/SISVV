<style>
    input[type="number"] {
        -webkit-appearance: none;
        /* Desactiva la apariencia predeterminada */
    }

    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        display: none;
        /* Oculta los botones */
    }
</style>
<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('lockers.nav')
    </x-slot>
    
    {{-- Contenido --}}
    <h4 class="text-2xl font-bold dark:text-white mx-2">ALTA LOCKERS</h4>
    {{-- Contenido --}}
    <livewire:lockers.movimientos.alta />
</x-app-layout>
