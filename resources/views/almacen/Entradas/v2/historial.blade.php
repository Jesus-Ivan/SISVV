<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('almacen.nav')
    </x-slot>
    {{-- Titulo --}}
    <div class="container p-3">
        <div class="flex ">
            <div class="inline-flex flex-grow">
                <h4 class="flex items-center text-2xl font-bold dark:text-white">HISTORIAL DE ENTRADAS v2</h4>
            </div>
        </div>
    </div>
    {{-- Componente livewire --}}
    <livewire:almacen.entradas.v2.historial />

</x-app-layout>
