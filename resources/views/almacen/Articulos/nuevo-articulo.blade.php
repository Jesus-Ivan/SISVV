<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('almacen.nav')
    </x-slot>

    {{-- Contenido --}}
    <div class="container py-2">
        <div class="flex ms-3">
            <h4 class="flex items-center text-2xl font-bold dark:text-white">NUEVO ARTÍCULO</h4>
        </div>
    </div>

    <div>
        <livewire:almacen.articulos.articulo-nuevo />
    </div>

    

</x-app-layout>
