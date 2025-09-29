<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('sistemas.nav')
    </x-slot>
    {{-- TITULO --}}
    <h4 class="text-2xl font-bold dark:text-white my-3">CREAR DETALLES CAJA</h4>
    {{-- Contenido --}}

    <form action="{{ route('sistemas.pv.detalles-caja') }}" method="POST">
        @csrf
        <div>
            <p>ADVERTENCIA</p>
            <p>Este metodo llena la tabla 'detalles_caja' segun su estado actual de las ventas</p>
            <p>Solo ejecutar una vez, si ya se va a desplegar la actualizacion de notas pendientes</p>
        </div>

        <button type="submit" class="px-3 py-2 bg-cyan-300 rounded-md hover:bg-cyan-500">
            LLENAR TABLA :o
        </button>
    </form>

</x-app-layout>
