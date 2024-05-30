<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('sistemas.nav')
    </x-slot>

    {{-- Contenido --}}
    <div class="flex items-center m-2">
        <!-- TITULO -->
        <h4 class="text-2xl font-bold dark:text-white mx-2">Cargar Registros</h4>
    </div>

    <div class="ms-3 mx-3">
        <form action="{{ route('subirRegistros') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="max-w-sm">
                <label for="registros" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Seleccionar
                    tipo de registro</label>
                <select id="registros" name="selectedType"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-fit p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="socios" selectd>SOCIOS</option>
                    <option value="integrantes">INTEGRANTES SOCIOS</option>
                    <option value="socios_membresias">RELACION DE MEMBRESIAS</option>
                    <option value="estados_cuenta">ESTADOS DE CUENTA</option>
                    <option value="cuotas">CUOTAS</option>
                    <option value="membresias">MEMBRESIAS</option>
                    <option value="catalogo">CATÁLOGO</option>
                    <option value="tipos_catalogo">TIPOS DE CATÁLOGO</option>
                </select>
            </div>

            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Seleccionar
                Archivo</label>
            <input
                class="block w-96 text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                aria-describedby="file_input_help" id="file_input" type="file" name="file_input"
                accept=".csv, .xlsx">
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">Solo archivos CSV.</p>
            <button type="submit"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                    height="24" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 7.205c4.418 0 8-1.165 8-2.602C20 3.165 16.418 2 12 2S4 3.165 4 4.603c0 1.437 3.582 2.602 8 2.602ZM12 22c4.963 0 8-1.686 8-2.603v-4.404c-.052.032-.112.06-.165.09a7.75 7.75 0 0 1-.745.387c-.193.088-.394.173-.6.253-.063.024-.124.05-.189.073a18.934 18.934 0 0 1-6.3.998c-2.135.027-4.26-.31-6.3-.998-.065-.024-.126-.05-.189-.073a10.143 10.143 0 0 1-.852-.373 7.75 7.75 0 0 1-.493-.267c-.053-.03-.113-.058-.165-.09v4.404C4 20.315 7.037 22 12 22Zm7.09-13.928a9.91 9.91 0 0 1-.6.253c-.063.025-.124.05-.189.074a18.935 18.935 0 0 1-6.3.998c-2.135.027-4.26-.31-6.3-.998-.065-.024-.126-.05-.189-.074a10.163 10.163 0 0 1-.852-.372 7.816 7.816 0 0 1-.493-.268c-.055-.03-.115-.058-.167-.09V12c0 .917 3.037 2.603 8 2.603s8-1.686 8-2.603V7.596c-.052.031-.112.059-.165.09a7.816 7.816 0 0 1-.745.386Z" />
                </svg>
                Importar Datos
            </button>
        </form>
    </div>

    <div class="ms-3 mx-3 my-2">
        {{-- Linea divisora --}}
        <hr class="h-px my-1 bg-gray-300 border-0 dark:bg-gray-700">

        {{-- Boton de regresar --}}
        <a type="button" href="{{ route('sistemas') }}"
            class="my-2 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
            <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 12h14M5 12l4-4m-4 4 4 4" />
            </svg>Regresar
        </a>
    </div>

    @if (session('success'))
        <div class="ms-3 text-sm font-medium">
            {{ session('success') }}
        </div>
    @else
        <div class="ms-3 text-sm font-medium">
            {{ session('fail') }}
        </div>
    @endif

</x-app-layout>
