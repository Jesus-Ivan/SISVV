<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('puntos.nav')
    </x-slot>

    {{-- Contenido --}}
    <div> {{-- Opcional: un contenedor para dar margen --}}
        @livewire('puntos.inventario.nueva-solicitud', [
            'codigopv' => $codigopv,
            'permisospv' => $permisospv,
        ])
    </div>

</x-app-layout>
