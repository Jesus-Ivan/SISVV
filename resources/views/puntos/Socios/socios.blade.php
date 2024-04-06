<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('puntos.nav')
    </x-slot>
    {{-- Contenido --}}
    <div>
        <!-- Title -->
        <h4 class="text-2xl font-bold dark:text-white m-2">Socios</h4>
        <div>
            <livewire:puntos.socios />
        </div>
    </div>
</x-app-layout>
