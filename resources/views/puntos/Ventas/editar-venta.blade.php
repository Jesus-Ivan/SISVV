<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('puntos.nav')
    </x-slot>

    {{-- Contenido --}}
    <div>
        <!-- Title -->
        <h4 class="uppercase text-2xl font-bold dark:text-white mx-2">VENTA {{ $venta->folio }} - {{ $venta->tipo_venta }}</h4>
        <div>
            <livewire:puntos.ventas.editar.container :permisospv="$permisospv" :venta="$venta->folio" :codigopv="$codigopv" />
        </div>
    </div>
</x-app-layout>
