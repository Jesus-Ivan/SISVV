<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('sistemas.nav')
    </x-slot>

    {{-- Contenido --}}
    <div class="flex items-center m-2">
        <!-- TITULO -->
        <h4 class="text-2xl font-bold dark:text-white mx-2">Obtener reporte mensual</h4>
    </div>
    <form action="{{ route('sistemas.reportes') }}" method="POST" target="_blank">
        @csrf
        <div class="flex gap-4">
            <div class="max-w-sm">
                <label for="registros" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Seleccionar
                    tipo</label>
                <select id="registros" name="selectedType"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-fit p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="V" selectd>VENTAS</option>
                    <option value="R">RECIBOS</option>
                </select>
            </div>
            <div class="max-w-sm">
                <label for="user" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Seleccionar
                    usuario</label>
                <select id="user" name="user"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-fit p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="{{ null }}" selectd>Seleccione</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <input type="date" id="fechaInicio" name="fechaInicio">
        <input type="date" id="fechaFin" name="fechaFin">
        <button type="submit">Generar</button>
    </form>
    <div class="ms-3 mx-3 my-2">
        {{-- Linea divisora --}}
        <hr class="h-px my-1 bg-gray-300 border-0 dark:bg-gray-700">
        {{-- Boton de regresar --}}
        <a type="button" href="{{ route('sistemas.reportes') }}"
            class="my-2 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
            <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 12h14M5 12l4-4m-4 4 4 4" />
            </svg>Regresar
        </a>
    </div>

</x-app-layout>
