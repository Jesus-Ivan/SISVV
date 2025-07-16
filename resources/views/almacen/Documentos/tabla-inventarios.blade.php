<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('almacen.nav')
    </x-slot>

    {{-- Titulo --}}
    <div class="container py-2">
        <h4 class="ms-3 text-2xl font-bold dark:text-white">TABLA DE INVENTARIOS SEMANAL</h4>
    </div>
    <div class="flex justify-center" x-data="{
        selectAll: false,
        selectedItems: [],
        allItems: {{ $grupos->pluck('id') }},
        toggleAll() {
            this.selectedItems = this.selectAll ? [] : [...this.allItems];
        },
        updateSelectAllState() {
            this.selectAll = this.selectedItems.length === this.allItems.length;
        }
    }">
        <form class="max-w-3xl" action="{{ route('almacen.documentos.inv-sem') }}" method="POST" target="_blank"
            id="reporte-form">
            @csrf
            {{-- BARRA DE BUSQUEDA --}}
            <div class="flex m-2 gap-4">
                <div class="flex gap-2 items-center">
                    {{-- SELECT BODEGA --}}
                    <div>
                        <select id="clave_bodega" name="clave_bodega"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-fit p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            @foreach ($bodegas as $index => $item)
                                <option wire:key='{{ $index }}' value="{{ $item->clave }}">
                                    {{ $item->descripcion }}
                                </option>
                            @endforeach
                        </select>
                        @error('clave_bodega')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    {{-- SELECT FECHA --}}
                    <div>
                        <input type="date" value="{{ $fecha }}" id="fecha" name="fecha"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        @error('fecha_inv')
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
            {{-- Tabla grupos insumos --}}
            <div class="p-3">
                <table class=" w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="w-16 px-4 py-3">
                                #
                            </th>
                            <th scope="col" class=" px-6 py-3">
                                DESCRIPCION
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <input type="checkbox" x-model="selectAll" value="!selectAll" x-on:click="toggleAll"
                                    class="w-6 h-6 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($grupos as $i_grupo => $grupo)
                            <tr wire:key='{{ $i_grupo }}'
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                                <td
                                    class="max-w-16 px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $grupo->id }}
                                </td>
                                <td class="max-w-32 font-medium text-gray-900  dark:text-white ">
                                    <div class="flex items-center">
                                        <label for="{{ $grupo->id }}"
                                            class="w-full px-6 py-3 text-sm font-medium text-gray-900 dark:text-gray-300">
                                            {{ $grupo->descripcion }}
                                        </label>
                                    </div>
                                </td>
                                <td class="w-4 px-6 py-3 ">
                                    <input id="{{ $grupo->id }}" name="selected_grupos[]" type="checkbox"
                                        value="{{ $grupo->id }}" x-model="selectedItems"
                                        x-on:change="updateSelectAllState"
                                        class="w-6 h-6 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</x-app-layout>