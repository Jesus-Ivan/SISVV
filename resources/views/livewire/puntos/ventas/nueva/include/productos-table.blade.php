<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class=" px-6 py-3">
                    TIEMPO
                </th>
                <th scope="col" class="px-6 py-3">
                    DESCRIPCION
                </th>
                <th scope="col" class="px-6 py-3">
                    OBSERVACIONES
                </th>
                <th scope="col" class="px-6 py-3">
                    PRECIO
                </th>
                <th scope="col" class="w-56 px-6 py-3">
                    CANTIDAD
                </th>
                <th scope="col" class=" px-6 py-3">
                    SUBTOTAL
                </th>
                <th scope="col" class=" px-6 py-3">
                    ACCIONES
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($this->ventaForm->productosTable as $productoIndex => $producto)
                <tr wire:key="{{ $productoIndex }}"
                    class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        @if (array_key_exists('catalogo_productos', $producto))
                            {{ $producto['tiempo'] }}
                        @else
                            <select id="tiempos" wire:model="ventaForm.productosTable.{{ $productoIndex }}.tiempo"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option selected value="{{ null }}"></option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                        @endif
                    </th>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        <div class="flex items-center">
                            {{-- <span class="flex w-4 h-4 me-2 bg-yellow-300 rounded-full"></span> --}}
                            <p>{{ array_key_exists('catalogo_productos', $producto) ? $producto['catalogo_productos']['nombre'] : $producto['nombre'] }}
                            </p>
                        </div>
                    </th>
                    <td class="px-6 py-4">
                        @if (array_key_exists('catalogo_productos', $producto))
                            <input type="text" id="disabled-input" aria-label="disabled input"
                                class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                value="{{ $producto['observaciones'] }}" disabled>
                        @else
                            <input type="text" id="observaciones"
                                wire:model="ventaForm.productosTable.{{ $productoIndex }}.observaciones"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        ${{ $producto['precio'] }}
                    </td>
                    <td class="px-6 py-4">
                        @if (array_key_exists('catalogo_productos', $producto))
                            <div class="flex gap-1 items-center">
                                <button type="button" wire:click="decrementar({{ $productoIndex }})"
                                    class="h-9 text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M5 12h14" />
                                    </svg>
                                    <span class="sr-only">Restar cantidad</span>
                                </button>
                                <input type="number" id="disabled-input" aria-label="disabled input"
                                    value="{{ $producto['cantidad'] }}"
                                    class=" bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
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
                            <input type="number" id="cantidad" min="0" max="100"
                                wire:model="ventaForm.productosTable.{{ $productoIndex }}.cantidad"
                                wire:change="updateQuantity({{ $productoIndex }}, $event.target.value)"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="1" required />
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        ${{ $producto['subtotal'] }}
                    </td>
                    <td class="px-6 py-4">
                        <button type="button" x-data x-on:click="$dispatch('open-modal', {name:'modal-modificadores'})"
                            class="text-gray-700 border border-gray-700 hover:bg-gray-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-gray-500 dark:text-gray-500 dark:hover:text-white dark:focus:ring-gray-800 dark:hover:bg-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-[20px] h-[20px]">
                                <path stroke="currentColor" stroke-linecap="round" stroke-width="3"
                                    d="M12 6h.01M12 12h.01M12 18h.01" />
                            </svg>
                            <span class="sr-only">modificadores</span>
                        </button>
                        @if (!array_key_exists('catalogo_productos', $producto))
                            <button type="button" wire:click="eliminarArticulo({{ $productoIndex }})"
                                class="text-red-700 border border-red-700 hover:bg-red-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:focus:ring-red-800 dark:hover:bg-red-500">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="w-5 h-5">
                                    <path fill-rule="evenodd"
                                        d="M8.586 2.586A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4a2 2 0 0 1 .586-1.414ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="sr-only">Borrar</span>
                            </button>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="font-semibold text-gray-900 dark:text-white">
                <th scope="row" class="px-6 py-3 text-base">Total</th>
                <td class="px-6 py-3"></td>
                <td class="px-6 py-3"></td>
                <td class="px-6 py-3"></td>
                <td class="px-6 py-3"></td>
                <td class="px-6 py-3">${{ $this->ventaForm->totalVenta }}</td>
            </tr>
        </tfoot>
    </table>
    @error('ventaForm.productosTable')
        <x-input-error messages="{{ $message }}" />
    @enderror
</div>
