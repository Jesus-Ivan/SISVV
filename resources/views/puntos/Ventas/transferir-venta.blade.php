<x-app-layout>
    {{-- Contenido --}}
    <div class="px-4 py-6">
        <!-- Title -->
        <h4 class="text-2xl font-bold dark:text-white mx-2">TRANSFERIR VENTA :{{ $venta->folio }} ENTRE PUNTOS
        </h4>
        <!-- Componente -->
        <livewire:puntos.ventas.transferir.container :venta="$venta"  />
    </div>
</x-app-layout>
