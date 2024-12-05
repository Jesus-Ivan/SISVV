<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('almacen.nav')
    </x-slot>

    {{-- Contenido --}}
    <div class="container py-3">
        <div class="flex ms-2">
            <div class="inline-flex flex-grow">
                <h4 class="flex items-center ms-2 text-2xl font-bold dark:text-white">Historial de traspasos</h4>
            </div>
        </div>
    </div>

    <livewire:almacen.traspasos.historial />
</x-app-layout>
