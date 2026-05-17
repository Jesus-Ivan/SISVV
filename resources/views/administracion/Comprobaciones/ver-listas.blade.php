<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('administracion.nav')
    </x-slot>
    <!-- Title -->
    <h4 class="text-2xl font-bold dark:text-white m-3">CONTROL DE GASTOS DEL MES</h4>
    {{-- Contenido --}}
    <div>
        <livewire:administracion.comprobaciones.ver-listas />
    </div>
</x-app-layout>