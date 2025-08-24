<div class="ms-3 mx-3" @keyup.ctrl.window="$dispatch('open-modal', {name:'modal-articulos'})">
    {{-- BOTÓN Y TITULO --}}
    <div>
        <div class="inline-flex flex-grow my-3">
            <button x-data x-on:click="$dispatch('open-modal', {name:'modal-articulos'})"
                class="w-fit text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                type="button">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd"
                        d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z"
                        clip-rule="evenodd" />
                </svg>
            </button>
            <h4 class="flex items-center ms-2 text-2xl font-bold dark:text-white">NUEVO TRASPASO v2</h4>
        </div>
    </div>
    {{-- BARRA DE DATOS --}}
    <div class="flex py-2 gap-3">
        {{-- BODEGA DE ORIGEN --}}
        <div>
            <select id="origen" id="disabled-input" aria-label="disabled input" disabled
                class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-fit p-1.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option selected value="{{ null }}">BODEGA ORIGEN</option>
            </select>
        </div>
        {{-- BODEGA DE DESTINO --}}
        <div>
            <select id="destino" id="disabled-input" aria-label="disabled input" disabled
                class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-fit p-1.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option selected value="{{ null }}">BODEGA DESTINO</option>
            </select>
        </div>
        {{-- OBSERVACIONES --}}
        <div class="flex grow">
            <input type="text"
                class="h-9 max-w-md bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Observaciones" />
        </div>
        {{-- FECHA --}}
        <div>
            <input type="date"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
        </div>
        {{-- HORA --}}
        <div>
            <input type="time"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
        </div>
    </div>

    {{-- CONTENIDO --}}
    {{-- TABLA DE PRESENTACIONES -> INSUMOS --}}
    @include('livewire.almacen.traspasos.v2.include.present_insum-table')
    {{-- TABLA DE PRESENTACIONES -> PRESENTACIONES --}}
    @include('livewire.almacen.traspasos.v2.include.present_present-table')
    {{-- TABLA DE INSUMOS -> PRESENTACIONES --}}
    @include('livewire.almacen.traspasos.v2.include.insum_present-table')
    {{-- TABLA DE INSUMOS -> INSUMOS --}}
    @include('livewire.almacen.traspasos.v2.include.insum_insum-table')

    {{-- BOTON DE CANCELAR --}}
    <a type="button" href="{{ route('almacen.traspasov2') }}"
        class="my-2 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
        <svg class="w-5 h-5 me-2 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
            height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M6 18 17.94 6M18 18 6.06 6" />
        </svg>
        Cancelar
    </a>
    {{-- BOTON DE GUARDAR --}}
    <button type="button"
        class="my-2 text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
        <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
            fill="currentColor" viewBox="0 0 24 24">
            <path fill-rule="evenodd"
                d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm10 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM8 7V5h8v2a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1Z"
                clip-rule="evenodd" />
        </svg>
        Guardar
    </button>

    {{-- MODAL DE ARTICULOS --}}
    <x-modal name="modal-articulos" title="AGREGAR PRESENTACIÓN / INSUMO">
        <x-slot name='body'>
            <!-- Modal content -->
            <div class="h-auto max-w-4xl overflow-y-auto">
                <!-- Modal body -->
                <div class="p-1 w-full max-w-2xl max-h-full">
                    {{-- BARRA DE BUSQUEDA --}}
                    <div class="relative">
                        <div class="inline-flex gap-2">
                            {{-- FOLIO REQUISICIÓN --}}
                            <input type="text"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Requisición" />
                            {{-- BODEGA DE ORIGEN --}}
                            <select id="bodega_origen"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-fit p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option selected value="{{ null }}">ORIGEN</option>
                            </select>
                            {{-- BODEGA DE DESTINO --}}
                            <select id="bodega_destino"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-fit p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option selected value="{{ null }}">DESTINO</option>
                            </select>
                            {{-- BARRA DE BUSQUEDA --}}
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                    </svg>
                                </div>
                                <input type="text"
                                    class="w-80 p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Código o Descripción" />
                            </div>
                        </div>
                    </div>
                    <!-- Result table-->
                    <div class="overflow-y-auto h-80 my-2">
                        <table class=" w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="p-4">
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        CODIGO
                                    </th>
                                    <th scope="col" class="py-3">
                                        DESCRIPCIÓN
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        C.C.IVA
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                                    <td class="w-4 p-4">
                                        <input id="checkbox-" type="checkbox"
                                            class="w-6 h-6 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    </td>
                                    <th scope="row"
                                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">

                                    </th>
                                    <td class="w-96 font-medium text-gray-900  dark:text-white">
                                        <div class="flex items-center">
                                            <label for="checkbox"
                                                class="w-96 py-4 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">

                                            </label>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-medium w-fit text-gray-900 dark:text-white">
                                        $
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Modal footer -->
                <div>
                    <button type="button"
                        class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Aceptar
                    </button>
                </div>
        </x-slot>
    </x-modal>
</div>
