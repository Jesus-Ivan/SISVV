<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('puntos.nav')
    </x-slot>

    {{-- Contenido --}}
    <div> 
        @livewire('puntos.inventario.nueva-solicitud', [
            'codigopv' => $codigopv,
            'permisospv' => $permisospv,
        ])
    </div>

</x-app-layout>
