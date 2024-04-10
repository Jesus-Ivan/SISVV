<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('cocina.nav')
    </x-slot>

    {{-- Contenido --}}
    <div>
        <!-- Title -->
        <h4 class="py-5 ms-3 text-2xl font-bold dark:text-white">Nueva Transformación</h4>
        <div>
            <livewire:cocina.transformaciones-nueva />
        </div>
    </div>
</x-app-layout>