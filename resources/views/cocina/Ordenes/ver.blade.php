<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('cocina.nav')
    </x-slot>
    {{-- Contenido --}}
    <div class="m-2">
        <!-- Title -->
        <div>
            <div class="flex">
                <h4 class="flex-1 text-2xl font-bold dark:text-white">Orden - {{ $folio }}</h4>
                <h4 class="text-2xl font-bold dark:text-white">12/12/2023 09:50</h4>
            </div>
            <div class="flex">
                <div class="inline-flex grow items-end gap-1">
                    <h6 class="text-lg font-bold dark:text-white">Cliente:</h6>
                    <p>FELIPE SEGUNDO PRIMERO</p>
                </div>
                <p>Cafeteria</p>
            </div>
        </div>
        <livewire:cocina.ordenes-ver />
    </div>
</x-app-layout>
