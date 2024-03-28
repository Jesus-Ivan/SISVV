<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('recepcion.nav')
    </x-slot>

    {{-- Contenido --}}
    <h4 class="text-2xl font-bold dark:text-white">Cobros</h4>

</x-app-layout>
