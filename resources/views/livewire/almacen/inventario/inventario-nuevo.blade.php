<div class="ms-3 mx-3">
    <div class="flex justify-between items-end">
        <div class="flex gap-2 items-end">
            {{-- BODEGA --}}
            <form class="w-fit">
                <label for="bodega" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Bodega</label>
                <select id="bodega" disabled wire:model='clave_bodega'
                    class="cursor-not-allowed bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected value="{{ null }}">SELECCIONAR BODEGA</option>
                    @foreach ($this->bodegas as $index => $item)
                        <option wire:key='{{ $index }}' value="{{ $item->clave }}">
                            {{ $item->descripcion }}
                        </option>
                    @endforeach
                </select>
            </form>
            {{-- FECHA --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Fecha Existencias</label>
                <input type="text" id="fecha" aria-label="disabled input" wire:model='fecha_inv'
                    class="w-32 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-1.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    disabled>
            </div>
            {{-- HORA --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Hora Existencias</label>
                <input type="text" id="hora" aria-label="disabled input" wire:model='hora_inv'
                    class="w-32 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block  p-1.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    disabled>
            </div>
            {{-- OBSERVACIONES --}}
            <div>
                <input type="text" wire:model='observaciones'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block  p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Observaciones" />
            </div>
        </div>

        <div class="flex gap-2 items-end">
            {{-- INVENTARIO TEORICO --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Inventario Teórico</label>
                <input type="text" id="inv-teorico" aria-label="disabled input" wire:model='total_inv_teorico'
                    class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    value="$ 0.00" disabled>
            </div>
            {{-- INVENTARIO FISICO --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Inventario Físico</label>
                <input type="text" id="inv-fisico" aria-label="disabled input" wire:model='total_inv_real'
                    class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    value="$ 0.00" disabled>
            </div>
            {{-- diferencia --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Diferencia</label>
                <input type="text" id="diferencia" aria-label="disabled input" wire:model='total_diferencia'
                    class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    value="$ 0.00" disabled>
            </div>
        </div>
    </div>

    {{-- TABLA DE INVENTARIOS --}}
    @if ($clave_bodega)
        @if ($this->bodegas->find($clave_bodega)->naturaleza == $presentaciones)
            @include('livewire.almacen.inventario.include.table-presentaciones')
        @else
            @include('livewire.almacen.inventario.include.table-insumos')
        @endif
    @endif

    {{-- BOTON DE CANCELAR --}}
    <a href="{{ route('almacen.inventario-fisico') }}"
        class="my-2 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800
        ">
        <svg class="w-5 h-5 me-2 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
            height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M6 18 17.94 6M18 18 6.06 6" />
        </svg>
        Cancelar
    </a>
    {{-- BOTON DE GUARDAR --}}
    <button wire:click ='guardar'
        class="my-2 text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800
        ">
        <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
            fill="currentColor" viewBox="0 0 24 24">
            <path fill-rule="evenodd"
                d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm10 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM8 7V5h8v2a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1Z"
                clip-rule="evenodd" />
        </svg>
        Guardar
    </button>

    {{-- MODAL GRUPOS PRESENTACIONES --}}
    <x-modal name="modal-grupo" title="Seleccione grupo de insumos">
        @if ($loaded)
            <x-slot name='body'>
                <div class="h-auto w-4xl overflow-y-auto">
                    <p>No puedes cargar otra vez las existencias</p>
                    <p>Reinicia la pagina en caso cambiar la fecha o bodega</p>
                </div>
            </x-slot>
        @else
            <x-slot name='body'>
                <!-- CONTENIDO NORMAL -->
                <div class="h-auto max-w-4xl overflow-y-auto" wire:loading.remove.remove wire:target='agregar'>
                    {{-- Search bar --}}
                    <div class="grid gap-2 grid-cols-3">
                        {{-- SELECT BODEGA --}}
                        <div>
                            <select wire:model='clave_bodega'
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="{{ null }}">Selecciona una bodega</option>
                                @foreach ($this->bodegas as $index => $item)
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
                            <input type="date" wire:model='fecha_inv'
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                            @error('fecha_inv')
                                <x-input-error messages="{{ $message }}" />
                            @enderror
                        </div>
                        {{-- SELECT HORA --}}
                        <div>
                            <input type="time" wire:model='hora_inv'
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                            @error('hora_inv')
                                <x-input-error messages="{{ $message }}" />
                            @enderror
                        </div>
                    </div>
                    {{-- Tabla grupos insumos --}}
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="w-16 px-4 py-3">
                                    #
                                </th>
                                <th scope="col" class=" px-6 py-3">
                                    DESCRIPCION
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    <input wire:model='seleccionar_general' wire:click='seleccionar()'
                                        type="checkbox"
                                        class="w-6 h-6 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($this->grupos as $i_grupo => $grupo)
                                <tr wire:key='{{ $i_grupo }}'
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                                    <td
                                        class="max-w-16 px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $grupo->id }}
                                    </td>
                                    <td class="max-w-32 font-medium text-gray-900  dark:text-white ">
                                        <div class="flex items-center">
                                            <label for="checkbox-{{ $grupo->id }}"
                                                class="w-full px-6 py-3 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                {{ $grupo->descripcion }}
                                            </label>
                                        </div>
                                    </td>
                                    <td class="w-4 px-6 py-3 ">
                                        <input id="checkbox-{{ $grupo->id }}"
                                            wire:model="lista_grupos.{{ $grupo->id }}" type="checkbox"
                                            class="w-6 h-6 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- Modal footer -->
                    <div
                        class="mt-2 flex items-center space-x-3 rtl:space-x-reverse border-t border-gray-200 rounded-b dark:border-gray-600">
                        <button wire:click='agregar()' type="button"
                            class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Aceptar
                        </button>
                    </div>
                </div>
                <!-- CONTENIDO CARGANDO -->
                <div class="w-96 h-80" wire:loading wire:target='agregar'>
                    <div
                        class="flex items-center justify-center w-full h-full border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                        <div 
                            class="text-lg px-4 py-2 leading-none text-center text-blue-800 bg-blue-200 rounded-full animate-pulse dark:bg-blue-900 dark:text-blue-200">
                            <p>Calculando existencias ...</p>
                            <p>Por favor espere</p>
                        </div>
                    </div>
                </div>
            </x-slot>
        @endif
    </x-modal>

    <!--Alerts-->
    <x-action-message on='open-action-message'>
        @if (session('success'))
            <div id="alert-exito"
                class="flex items-center p-4 mb-4 text-green-800 border-t-4 border-green-300 bg-green-50 dark:text-green-400 dark:bg-gray-800 dark:border-green-800"
                role="alert">
                <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <div class="ms-3 text-sm font-medium">
                    {{ session('success') }}
                </div>
            </div>
        @else
            <div id="alert-error"
                class="flex items-center p-4 mb-4 text-red-800 border-t-4 border-red-300 bg-red-50 dark:text-red-400 dark:bg-gray-800 dark:border-red-800"
                role="alert">
                <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <div class="ms-3 text-sm font-medium">
                    {{ session('fail') }}
                </div>
            </div>
        @endif
    </x-action-message>

    <!--INDICADOR DE CARGA-->
    <div wire:loading wire:target='guardar'>
        <x-loading-screen name='loading'>
            <x-slot name='body'>
                <div class="flex">
                    <div class="me-4">
                        @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
                    </div>
                    <p>Guardando ajustes de inventario ... </p>
                </div>
            </x-slot>
        </x-loading-screen>
    </div>
</div>
