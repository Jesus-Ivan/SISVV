<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('recepcion.nav')
    </x-slot>
    {{-- Contenido --}}
    <div>
        <!-- Title -->
        <h4 class="text-2xl font-bold dark:text-white mx-2">Nuevo cargo a estado de cuenta</h4>
        <!-- Componente -->
        <livewire:recepcion.cargos-nuevo/>
    </div>
</x-app-layout>
