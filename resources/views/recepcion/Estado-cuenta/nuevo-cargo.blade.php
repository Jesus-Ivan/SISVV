<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('recepcion.nav')
    </x-slot>
    {{-- Contenido --}}
    <div class="mx-2">
        <!-- Nuevo cargo a estado de cuenta -->
        <h4 class="text-2xl font-bold dark:text-white ">Nuevo cargo a estado de cuenta</h4>
        <!-- Componente -->
        <livewire:recepcion.estados.cargos-nuevo :socio="$socio" />
    </div>
</x-app-layout>
