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
        @include('almacen.nav')
    </x-slot>
    {{-- Tittle --}}
    <div>
        <div class="my-1">
            <h4 class="flex items-center ms-2 text-2xl font-bold dark:text-white">EDITAR ENTRADA {{ $folio }}</h4>
        </div>
    </div>
    <livewire:almacen.entradas.v2.editar folio="{{ $folio }}" />
</x-app-layout>
