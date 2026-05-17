<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('cocina.nav')
    </x-slot>
    {{-- Contenido --}}
    <div class="m-2">
        <!-- Title -->
        <h4 class="text-2xl font-bold dark:text-white">Reporte produccion</h4>
        <p class=" text-gray-500 dark:text-gray-400">
            Esta sección permite generar un reporte en formato XLS, que contiene todas las comandas capturadas en el
            sistema.
        </p>
        <p class="mb-3 text-gray-500 dark:text-gray-400">
            Para generar el reporte es necesario especificar un rango de fecha y al menos 1 punto de venta (o todos).
        </p>

        <div class="flex justify-center" x-data="{
            selectAll: false,
            selectedItems: [],
            allItems: {{ $puntos->pluck('clave') }},
            toggleAll() {
                this.selectedItems = this.selectAll ? [] : [...this.allItems];
            },
            updateSelectAllState() {
                this.selectAll = this.selectedItems.length === this.allItems.length;
            }
        }">
            <form class="max-w-3xl" action="{{ route('cocina.ordenes.reporte') }}" method="POST" target="_blank"
                id="reporte-form">
                @csrf
                {{-- BARRA DE BUSQUEDA --}}
                <div class="flex m-1 gap-4">
                    <div class="flex gap-2 items-center">
                        {{-- SELECT FECHA --}}
                        <div>
                            <input type="date" value="{{ $hoy }}" id="fecha" name="f_inicio"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                            @error('fecha')
                                <x-input-error messages="{{ $message }}" />
                            @enderror
                        </div>
                        <p>-></p>
                        <div>
                            <input type="date" value="{{ $hoy }}" id="fecha" name="f_fin"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                            @error('fecha')
                                <x-input-error messages="{{ $message }}" />
                            @enderror
                        </div>
                    </div>
                    <div class="flex grow">
                    </div>
                    <div>
                        <button type="submit"
                            class="h-11 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Generar</button>
                    </div>
                </div>
                {{-- CHECKBOX --}}
                <div class="flex items-center ">
                    <input id="eliminados" name="eliminados" type="checkbox"
                        class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <label for="eliminados"
                        class="ms-2 p-1 text-sm font-medium text-gray-900 dark:text-gray-300">Incluir eliminados</label>
                </div>
                {{-- Tabla puntos de venta --}}
                <div class="p-3 overflow-y-auto">
                    <table class=" w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="w-16 px-4 py-2">
                                    #
                                </th>
                                <th scope="col" class=" px-6 py-2">
                                    PUNTO VENTA
                                </th>
                                <th scope="col" class="px-6 py-2">
                                    <input type="checkbox" x-model="selectAll" value="!selectAll" x-on:click="toggleAll"
                                        class="w-6 h-6 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($puntos as $i => $pv)
                                <tr wire:key='{{ $i }}'
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                                    <td
                                        class="max-w-16 px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $i }}
                                    </td>
                                    <td class="max-w-32 font-medium text-gray-900  dark:text-white ">
                                        <div class="flex items-center">
                                            <label for="{{ $pv->clave }}"
                                                class="w-full px-6 py-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                {{ $pv->nombre }}
                                            </label>
                                        </div>
                                    </td>
                                    <td class="w-4 px-6 py-2 ">
                                        <input id="{{ $pv->clave }}" name="selected_pv[]" type="checkbox"
                                            value="{{ $pv->clave }}" x-model="selectedItems"
                                            x-on:change="updateSelectAllState"
                                            class="w-6 h-6 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @error('selected_grupos')
                        <x-input-error messages="{{ $message }}" />
                    @enderror
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
