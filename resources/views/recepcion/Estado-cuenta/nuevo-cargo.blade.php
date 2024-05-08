<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('recepcion.nav')
    </x-slot>
    {{-- Contenido --}}
    <div class="mx-2">
        <!-- Title -->
        <h4 class="text-2xl font-bold dark:text-white ">Nuevo cargo a estado de cuenta</h4>
        <!-- Componente -->
        <livewire:recepcion.estados.cargos-nuevo :socio="$socio" year="{{$year}}" month="{{$month}}" />
    </div>
</x-app-layout>
