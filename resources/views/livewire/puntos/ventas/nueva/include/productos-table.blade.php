<style>
    input[type="number"] {
        -webkit-appearance: none;
        /* Desactiva la apariencia predeterminada */
    }

    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        display: none;
        /* Oculta los botones */
    }
</style>
<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class=" w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 ">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class=" px-3 py-2">
                    TIEMPO
                </th>
                <th scope="col" class="px-3 py-2">
                    DESCRIPCION
                </th>
                <th scope="col" class="px-3 py-2">
                    OBSERVACIONES
                </th>
                <th scope="col" class="px-3 py-2">
                    PRECIO
                </th>
                <th scope="col" class="w-32 px-6 py-2">
                    CANTIDAD
                </th>
                <th scope="col" class=" px-3 py-2">
                    SUBTOTAL
                </th>
                <th scope="col" class=" px-3 py-2">
                    ACCIONES
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($this->ventaForm->productosTable as $productoIndex => $producto)
                @if (!array_key_exists('moved', $producto))
                    <tr wire:key="{{ $productoIndex }}"
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                        {{-- TIEMPO --}}
                        <th scope="row"
                            class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            @if (array_key_exists('catalogo_productos', $producto))
                                {{ $producto['tiempo'] }}
                            @else
                                <select wire:model="ventaForm.productosTable.{{ $productoIndex }}.tiempo"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option selected value="{{ null }}"></option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                </select>
                            @endif
                        </th>
                        {{-- DESCRIPCION --}}
                        <th scope="row"
                            class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <div class="flex items-center">
                                {{-- <span class="flex w-4 h-4 me-2 bg-yellow-300 rounded-full"></span> --}}
                                <p>{{ $producto['nombre'] ?: $producto['catalogo_productos']['nombre'] }}
                                </p>
                            </div>
                        </th>
                        <td class="px-3 py-2">
                            @if (array_key_exists('catalogo_productos', $producto))
                                <input type="text" aria-label="disabled input"
                                    class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    value="{{ $producto['observaciones'] }}" disabled>
                            @else
                                <input type="text"
                                    wire:model="ventaForm.productosTable.{{ $productoIndex }}.observaciones"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                            @endif
                        </td>
                        <td class="px-3 py-2">
                            ${{ $producto['precio'] }}
                        </td>
                        {{-- INPUTS DE CANTIDAD --}}
                        <td class="px-3 py-2">
                            @if (array_key_exists('catalogo_productos', $producto))
                                <div class="flex gap-1 items-center">
                                    @if ($this->ventaForm->permisospv->clave_rol != 'MES')
                                        <button type="button" wire:click="decrementar({{ $productoIndex }})"
                                            class="h-9 text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M5 12h14" />
                                            </svg>
                                            <span class="sr-only">Restar cantidad</span>
                                        </button>
                                    @endif
                                    <input type="number" id="disabled-input" aria-label="disabled input"
                                        value="{{ $producto['cantidad'] }}"
                                        class="w-14 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        disabled>
                                    <button type="button" wire:click="incrementar({{ $productoIndex }})"
                                        class="h-9 text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="M5 12h14m-7 7V5" />
                                        </svg>
                                        <span class="sr-only">Agregar cantidad</span>
                                    </button>
                                </div>
                            @else
                                {{ $producto['cantidad'] }}
                            @endif
                        </td>
                        <td class="px-3 py-2">
                            ${{ $producto['subtotal'] }}
                        </td>
                        <td class="px-3 py-2">
                            @if (!array_key_exists('catalogo_productos', $producto))
                                @if (!array_key_exists('modif', $producto))
                                    <button type="button" wire:click="eliminarArticulo({{ $producto['time'] }})"
                                        class="text-red-700 border border-red-700 hover:bg-red-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm py-1.5 px-3 text-center inline-flex items-center me-2 dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:focus:ring-red-800 dark:hover:bg-red-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                            class="w-5 h-5">
                                            <path fill-rule="evenodd"
                                                d="M8.586 2.586A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4a2 2 0 0 1 .586-1.414ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span class="sr-only">Borrar</span>
                                    </button>
                                @endif
                            @else
                                <button type="button" wire:click='transferir({{ $productoIndex }})'
                                    class="text-gray-900 hover:text-white border border-gray-800 hover:bg-gray-900 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm py-1.5 px-3 text-center  dark:border-gray-600 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-800">
                                    <svg class="w-6 h-6 rotate-90" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M8 20V7m0 13-4-4m4 4 4-4m4-12v13m0-13 4 4m-4-4-4 4" />
                                    </svg>
                                </button>
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr class="font-semibold text-gray-900 dark:text-white">
                <th scope="row" class="px-6 py-3 text-base">Total</th>
                <td class="px-3 py-2"></td>
                <td class="px-3 py-2"></td>
                <td class="px-3 py-2"></td>
                <td class="px-3 py-2"></td>
                <td class="px-3 py-2">${{ $this->ventaForm->totalVenta }}</td>
            </tr>
        </tfoot>
    </table>
    @error('ventaForm.productosTable')
        <x-input-error messages="{{ $message }}" />
    @enderror
</div>
