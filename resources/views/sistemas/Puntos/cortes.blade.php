<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('sistemas.nav')
    </x-slot>

    {{-- TITULO --}}
    <h4 class="text-2xl font-bold dark:text-white ms-3 my-3">REVISAR DETALLES CORTE</h4>

    {{-- Contenido --}}
    <div>
        <livewire:sistemas.puntos.cortes />
    </div>

</x-app-layout>
