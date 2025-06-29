<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('almacen.nav')
    </x-slot>

    {{-- Contenido --}}
    <div>
        <livewire:almacen.requisiciones.editar-requisicion folio_requi='{{ $folio }}'
            tittle='EDITAR REQUISICION: {{ $folio }}' />
    </div>
</x-app-layout>
