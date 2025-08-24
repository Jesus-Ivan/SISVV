<div>
    {{-- Contenido --}}
    <div class="container py-3">
        <div class="flex">
            <div class="px-3 flex-grow">
                <h4 class=" items-center text-2xl font-bold dark:text-white">ORDENES DE COMPRA</h4>
            </div>

            <div>
                <a type="button" href="{{ route('almacen.ordenes.historial') }}"
                    class="text-green-700 border border-green-700 hover:bg-green-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-green dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:focus:ring-green-800 dark:hover:bg-green-500">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                        <path fill-rule="evenodd"
                            d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z"
                            clip-rule="evenodd" />
                    </svg>
                    Ver Historial
                </a>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="ms-3 mx-3" wire:loading.class='animate-pulse pointer-events-none' wire:target='order'>
        <div class="relative  shadow-md sm:rounded-lg">
            <div class="flex items-center gap-5">
                {{-- FECHA DE INICIO --}}
                <input type="date" id="fInicio" wire:model='f_inicio'
                    class="block p-2 text-sm text-gray-900 border border-gray-300 rounded-lg w-64 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Buscar">
                <p>Al:</p>
                {{-- FECHA DE FIN --}}
                <input type="date" id="fFin" wire:model='f_fin'
                    class="block p-2 text-sm text-gray-900 border border-gray-300 rounded-lg w-64
                     bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Buscar">
                {{-- BOTON DE BUSQUEDA --}}
                <button type="button" wire:click='buscar'
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    <svg wire:loading.remove wire:target='buscar' class="w-6 h-6" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                            d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                    </svg>
                    <!--Loading indicator-->
                    <div wire:loading wire:target='buscar'>
                        @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
                    </div>
                    <span class="sr-only">Buscar</span>
                </button>
                {{-- CHECK DE ORDENAR POR PROVEEDOR --}}
                <div class="flex items-center">
                    <input id="order" type="checkbox" wire:model.live='order'
                        class="w-5 h-5 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800" />
                    <label for="order" class="p-2 text-sm font-medium text-gray-900 dark:text-gray-300">Ordenar por
                        proveedor</label>
                </div>
            </div>
            <div class="overflow-x-auto ">
                <table class=" w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                FOLIO
                            </th>
                            <th scope="col" class="px-6 py-3">
                                FECHA
                            </th>
                            <th scope="col" class="px-4 py-3">
                                SUBTOTAL
                            </th>
                            <th scope="col" class="px-6 py-3">
                                IVA
                            </th>
                            <th scope="col" class="px-4 py-3">
                                TOTAL
                            </th>
                            <th scope="col" class="px-4 py-3">
                                TIPO DE ORDEN
                            </th>
                            <th scope="col" class="px-6 py-3">
                                ACCIONES
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->ordenes as $index => $orden)
                            <tr wire:key ="{{ $index }}"
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-2">
                                    {{ $orden->folio }}
                                </td>
                                <td class="px-6 py-2">
                                    {{ $orden->fecha }}
                                </td>
                                <td class="px-6 py-2">
                                    ${{ $orden->subtotal }}
                                </td>
                                <td class="px-6 py-2">
                                    ${{ $orden->iva }}
                                </td>
                                <td class="px-6 py-2">
                                    ${{ $orden->total }}
                                </td>
                                <td class="px-6 py-2">
                                    {{ $orden->tipo_orden }}
                                </td>
                                <td class="px-6 py-2">
                                    <div class="flex">
                                        {{-- IMPRIMIR --}}
                                        <a type="button"
                                            href="{{ route('orden', ['folio' => $orden->folio, 'order' => $order]) }}"
                                            target="_blank"
                                            class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-3.5 py-1.5 text-center dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-600 dark:focus:ring-blue-800">
                                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24"
                                                height="24" fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd"
                                                    d="M8 3a2 2 0 0 0-2 2v3h12V5a2 2 0 0 0-2-2H8Zm-3 7a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h1v-4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v4h1a2 2 0 0 0 2-2v-5a2 2 0 0 0-2-2H5Zm4 11a1 1 0 0 1-1-1v-4h8v4a1 1 0 0 1-1 1H9Z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div>
                {{ $this->ordenes->links() }}
            </div>
        </div>
    </div>
</div>
