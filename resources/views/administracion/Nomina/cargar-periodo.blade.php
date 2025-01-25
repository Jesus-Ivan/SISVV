<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('administracion.nav')
    </x-slot>
    <!-- Title -->
    <h4 class="text-2xl font-bold dark:text-white m-4">CARGAR PERIODO</h4>
    {{-- Contenido --}}
    <form class="mx-4" action="{{ route('administracion.cargar-p') }}" method="POST" enctype="multipart/form-data">
        @csrf
        {{-- Selector de periodo --}}
        <div class="flex gap-4">
            {{-- FECHA INICIO --}}
            <div class="mb-5">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Inicio periodo</label>
                <input type="date" id="fInicio" name="fInicio" value="{{ $inicio_periodo }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            </div>
            {{-- FECHA FIN --}}
            <div class="mb-5">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fin periodo</label>
                <input type="date" id="fFin" name="fFin" value="{{ $fin_periodo }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            </div>
        </div>
        {{-- Selector de archivos --}}
        <div class="max-w-lg">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="user_avatar">Subir
                archivo</label>
            <input
                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                aria-describedby="nomina_help" id="nomina" name="nomina" type="file" required>
            <div class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="nomina_help">Archivos csv o xls</div>
        </div>
        {{-- Submit button --}}
        <div>
            <button type="submit"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Subir informacion</button>
        </div>

        {{-- Session mesage --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-2">
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-2">
                <p>{{ session('error') }}</p>
            </div>
        @endif


    </form>

</x-app-layout>
