<x-app-layout>
    {{-- Contenido --}}
    <div class="px-4 py-6">
        <!-- Title -->
        <h4 class="text-2xl font-bold dark:text-white mx-2">VENTA PENDIENTE {{ $folioventa }} - {{ $codigopv }}
        </h4>
        <!-- Componente -->
        <livewire:puntos.ventas.pagar.container :permisospv="$permisospv" folio="{{ $folioventa }}" codigopv="{{$codigopv}}" />
    </div>
</x-app-layout>
