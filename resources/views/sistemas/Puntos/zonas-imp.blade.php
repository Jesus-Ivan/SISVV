<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('sistemas.nav')
    </x-slot>

    <form action="{{ route('sistemas.pv.zonas-impresion') }}" method="POST">
        @csrf
        {{-- TITULO --}}
        <h4 class="text-2xl font-bold dark:text-white ms-3 my-3">Generar tabla de zonas de impresion</h4>
        <p>Este metodo asigna la zona de impresion por defecto de todos los productos con la marca "print default"</p>
        <p>ADVERTENCIA: Solo ejecutar 1 vez, cuando se libere las tablets</p>
    
            <x-primary-button>
                generar
            </x-primary-button>
    </form>
</x-app-layout>
