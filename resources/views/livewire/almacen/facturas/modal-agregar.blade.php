<div>
    <x-modal name="agregar-presentacion" title="AGREGAR PRESENTACIÓN">
        <x-slot:body>
            <div class="p-1 w-full max-w-2xl max-h-full">
                <div class="relative mt-1">
                    <div class="inline-flex gap-2">
                        <!-- SELECT PARA BUSCAR POR GRUPO -->
                        <select id="grupo" wire:model.live='selectedGrupo'
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option selected value="{{ null }}">SELECCIONAR GRUPO</option>
                            @foreach ($this->grupos as $grupo)
                                <option value="{{ $grupo->id }}">{{ $grupo->descripcion }}</option>
                            @endforeach
                        </select>
                        <!-- BARRA DE BUSQUEDA -->
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input type="text"
                                wire:model.live.debounce.500ms="searchPresentacion"
                                class="w-96 p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Código o Descripción" />
                        </div>
                        <!--Loading indicator-->
                        <div wire:loading.delay.long wire:target='searchPresentacion'>
                            @include('livewire.utils.loading', ['w' => 5, 'h' => 5])
                        </div>
                    </div>
                </div>
                <!-- TABLA DE RESULTADOS -->
                <div class="overflow-y-auto h-80 my-2">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="p-4">
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    CLAVE
                                </th>
                                <th scope="col" class="py-3">
                                    DESCRIPCIÓN
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    COSTO
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($this->presentaciones as $presentacion)
                                <tr
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                                    <td class="w-4 p-4">
                                        <input id="checkbox-{{ $presentacion->clave }}" type="checkbox"
                                            wire:model="selectedPresentacion.{{ $presentacion->clave }}"
                                            class="w-6 h-6 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    </td>
                                    <th scope="row"
                                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $presentacion->clave }}
                                    </th>
                                    <td class="w-96 font-medium text-gray-900  dark:text-white">
                                        <div class="flex items-center">
                                            <label for="checkbox-{{ $presentacion->clave }}"
                                                class="w-96 py-4 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                {{ $presentacion->descripcion }}
                                            </label>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-medium w-fit text-gray-900 dark:text-white">
                                        ${{ $presentacion->costo }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </x-slot>
        <x-slot:footer>
            <button type="button" wire:click='finalizarSeleccion'
                class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Aceptar
            </button>
        </x-slot>
    </x-modal>
</div>
