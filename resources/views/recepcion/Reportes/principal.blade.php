<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('recepcion.nav')
    </x-slot>

    <div class="m-3">
        {{-- CUENTAS POR COBRAR O CARTERA VENCIDA --}}
        <form action="{{ route('recepcion.reportes.vencidos') }}" method="POST" target="_blank">
            @csrf
            <!-- Title -->
            <h4 class="text-2xl font-bold dark:text-white p-2">Cartera de clientes vencidos</h4>
            <div class="flex gap-2 items-center">
                {{-- Tipo de archivo --}}
                <div class="w-72">
                    <div>
                        <label for="typeFile"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Formato</label>
                        <select id="typeFile" name="typeFile"
                            class="block w-full p-2.5 mb-3 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option selected value="PDF">PDF</option>
                            <option value="XLS">EXCEL</option>
                        </select>
                    </div>
                </div>
                {{-- Fecha Limite --}}
                <div class="w-72">
                    <label for="limite" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fecha
                        limite</label>
                    <input type="date" id="limite" name="limite" value="{{$limite}}"
                        class="mb-3 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        required />
                </div>
                {{-- Check box --}}
                <div>
                    {{-- CASILLA DE CUENTAS X COBRAR --}}
                    <div class="flex items-center">
                        <input id="consumosMesFin" type="checkbox" name="consumosMesFin"
                            class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="consumosMesFin" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                            Incluir notas limite
                        </label>
                    </div>
                    {{-- CASILLA DE CANCELADOS --}}
                    <div class="flex items-center mt-1">
                        <input id="cancelados" type="checkbox" name="cancelados"
                            class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="cancelados" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                            Incluir socios cancelados
                        </label>
                    </div>
                </div>
                <!--Boton de busqueda -->
                <button type="submit"
                    class="mt-7 w-32 h-11 justify-center text-center inline-flex items-center text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">
                    Buscar
                </button>
            </div>
        </form>

        {{-- REPORTE GENERAL DE RECIBOS POR USUARIO --}}
        <form action="{{ route('reportes.recibos') }}" method="POST" target="_blank">
            @csrf
            <!-- Title -->
            <h4 class="text-2xl font-bold dark:text-white p-2">Reporte de recibos por usuario</h4>
            <div class="flex gap-4 items-end">
                <div class="max-w-sm">
                    <label for="user"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Seleccionar
                        usuario</label>
                    <select id="user" name="user"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-fit p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="{{ null }}" selectd>Seleccione</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Fecha Inicio --}}
                <div class="w-72">
                    <label for="fechaInicio"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Inicio</label>
                    <input type="date" name="fechaInicio"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                </div>
                {{-- Fecha fin --}}
                <div class="w-72">
                    <label for="fechaFin"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fin</label>
                    <input type="date" name="fechaFin"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                </div>
                <!--Boton de busqueda -->
                <button type="submit"
                    class="w-32 h-11 justify-center text-center inline-flex items-center text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">
                    Buscar
                </button>
            </div>
        </form>
        {{-- REPORTE DE RECIBOS POR SOCIO --}}
        <form action="{{ route('reportes.recibos-socio') }}" method="POST" target="_blank">
            @csrf
            <!-- Title -->
            <h4 class="text-2xl font-bold dark:text-white p-2">Recibos del socio</h4>
            <div class="flex gap-4 items-end">
                {{-- ID SOCIO --}}
                <div>
                    <label for="no_socio" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No.
                        Socio</label>
                    <input type="number" id="no_socio" name="no_socio"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        required />
                </div>
                <!--Boton de busqueda -->
                <button type="submit"
                    class="w-32 h-11 justify-center text-center inline-flex items-center text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">
                    Buscar
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
