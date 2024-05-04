<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('almacen.nav')
    </x-slot>

    <div>
        <livewire:almacen.salidas.salidas-nueva />
    </div>

</x-app-layout>
