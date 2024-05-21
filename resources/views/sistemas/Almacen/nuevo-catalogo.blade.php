<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('sistemas.nav')
    </x-slot>

    <div>
        <livewire:sistemas.almacen.catalogo.catalogo-nuevo/>
    </div>

</x-app-layout>