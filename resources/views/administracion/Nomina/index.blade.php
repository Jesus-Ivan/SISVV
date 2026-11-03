<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('administracion.nav')
    </x-slot>

    {{-- Contenido del Control de Nómina --}}
    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <livewire:administracion.nomina.principal />
    </div>
</x-app-layout>

