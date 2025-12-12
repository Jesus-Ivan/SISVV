<div>
    <!--Info del socio-->
    <div class="m-3">
        <p>NOMBRE: {{ $venta->id_socio }} - {{ $venta->nombre }}</p>
        <p>TIPO VENTA: {{ $venta->tipo_venta }}</p>
        <div class="flex justify-between">
            <p>CORTE CAJA: {{ $venta->corte_caja }}</p>
            <p>CORTE APERTURA: {{ $caja->fecha_apertura }}</p>
            <p>CORTE CIERRE: {{ $caja->fecha_cierre }}</p>
        </div>
        <div class="flex justify-between">
            <p>FECHA APERTURA: {{ $venta->fecha_apertura }}</p>
            <p>FECHA CIERRE: {{ $venta->fecha_cierre }}</p>
        </div>
    </div>
    <!--Linea -->
    <hr class="h-px my-2 bg-gray-300 border-0 dark:bg-gray-700">
    {{-- TABLAS --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <!--Tabla de articulos-->
            <div class="mt-12 overflow-y-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                DESCRIPCION
                            </th>
                            <th scope="col" class="px-6 py-3">
                                PRECIO
                            </th>
                            <th scope="col" class="px-6 py-3">
                                CANTIDAD
                            </th>
                            <th scope="col" class="px-6 py-3">
                                SUBTOTAL
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->ventaForm->productosTable as $item)
                            <tr
                                class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                <th scope="row"
                                    class="w-full px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $item['nombre'] }}
                                </th>
                                <td class="px-6 py-2">
                                    ${{ $item['precio'] }}
                                </td>
                                <td class="px-6 py-2">
                                    {{ $item['cantidad'] }}
                                </td>
                                <td class="px-6 py-2">
                                    ${{ $item['subtotal'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="font-semibold text-gray-900 dark:text-white">
                            <th scope="row" class="px-6 py-3 text-base">Total Productos</th>
                            <td class="px-6 py-3"></td>
                            <td class="px-6 py-3"></td>
                            <td class="px-6 py-3">
                                ${{ $this->ventaForm->totalVenta }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div>
            <!--Boton de metodos de pago -->
            <div class="flex justify-end">
                <button type="button" x-data x-on:click="$dispatch('open-modal', {name:'modal-pagos'})"
                    class=" inline-flex items-center focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                    <svg class="w-6 h-6 text-white dark:text-gray-800 me-2" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                        viewBox="0 0 24 24">
                        <path fill-rule="evenodd"
                            d="M12 14a3 3 0 0 1 3-3h4a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-4a3 3 0 0 1-3-3Zm3-1a1 1 0 1 0 0 2h4v-2h-4Z"
                            clip-rule="evenodd" />
                        <path fill-rule="evenodd"
                            d="M12.293 3.293a1 1 0 0 1 1.414 0L16.414 6h-2.828l-1.293-1.293a1 1 0 0 1 0-1.414ZM12.414 6 9.707 3.293a1 1 0 0 0-1.414 0L5.586 6h6.828ZM4.586 7l-.056.055A2 2 0 0 0 3 9v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2h-4a5 5 0 0 1 0-10h4a2 2 0 0 0-1.53-1.945L17.414 7H4.586Z"
                            clip-rule="evenodd" />
                    </svg>
                    Añadir pago
                </button>
            </div>
            <!--Tabla de metodos de pagos -->
            <div class="relative overflow-y-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                NO.SOCIO
                            </th>
                            <th scope="col" class="px-6 py-3">
                                METODO DE PAGO
                            </th>
                            <th scope="col" class="px-6 py-3">
                                PROPINA
                            </th>
                            <th scope="col" class="px-6 py-3">
                                SUBTOTAL
                            </th>
                            <th scope="col" class="px-6 py-3">
                                ACCIONES
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->ventaForm->pagosTable as $index => $pago)
                            <tr wire:key ="{{ $index }}"
                                class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $pago['id_socio'] }}
                                </th>
                                <td class="px-6 py-4">
                                    @if ($pago['editable'])
                                        <select wire:model="ventaForm.pagosTable.{{ $index }}.id_tipo_pago"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            <option>Selecciona</option>
                                            @foreach ($this->metodosPago as $metodo)
                                                <option value="{{ $metodo->id }}">{{ $metodo->descripcion }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        {{ $pago['tipo_pago']['descripcion'] }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 ">
                                    <div class="flex gap-2 items-center">
                                        $<input type="number"
                                            wire:model="ventaForm.pagosTable.{{ $index }}.propina"
                                            class="{{ !$pago['editable'] ? 'opacity-50 pointer-events-none' : '' }}  bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2 items-center">
                                        @if (array_key_exists('id', $pago))
                                            $<input type="number"
                                                wire:model.live.debounce.500ms ="ventaForm.pagosTable.{{ $index }}.monto"
                                                class="{{ !$pago['editable'] ? 'opacity-50 pointer-events-none' : '' }} bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                                        @else
                                            $<input type="number"
                                                wire:model.live.debounce.500ms="ventaForm.pagosTable.{{ $index }}.monto_pago"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                                        @endif

                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if (!array_key_exists('id', $pago))
                                        <button type="button" wire:click="eliminarPago({{ $index }})"
                                            class="text-red-700 border border-red-700 hover:bg-red-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:focus:ring-red-800 dark:hover:bg-red-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                fill="currentColor" class="w-5 h-5">
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
                            <th scope="row" class="px-6 py-3 text-base">
                                <p>Total Pago</p>
                            </th>
                            <td class="px-6 py-3"></td>
                            <td class="px-6 py-3"></td>
                            <td class="px-6 py-3">
                                <p>${{ array_sum(array_column($this->ventaForm->pagosTable, 'monto')) + array_sum(array_column($this->ventaForm->pagosTable, 'monto_pago')) }}
                                </p>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <!--Botones de navegacion (cancelar, pagar venta)-->
    <div class="my-3">
        <button x-on:click="$dispatch('cerrar-pagina')"
            class="inline-flex items-center focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">
            <svg class="w-6 h-6 dark:text-gray-800 text-white me-2" aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                viewBox="0 0 24 24">
                <path fill-rule="evenodd"
                    d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm7.707-3.707a1 1 0 0 0-1.414 1.414L10.586 12l-2.293 2.293a1 1 0 1 0 1.414 1.414L12 13.414l2.293 2.293a1 1 0 0 0 1.414-1.414L13.414 12l2.293-2.293a1 1 0 0 0-1.414-1.414L12 10.586 9.707 8.293Z"
                    clip-rule="evenodd" />
            </svg>
            Cancelar
        </button>
        <button type="button" wire:click='pagarVentaPendiente' wire:loading.attr="disabled"
            wire:target='pagarVentaPendiente'
            class="inline-flex items-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
            <svg class="me-2 w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                viewBox="0 0 24 24">
                <path fill-rule="evenodd"
                    d="M7 6a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-2v-4a3 3 0 0 0-3-3H7V6Z"
                    clip-rule="evenodd" />
                <path fill-rule="evenodd"
                    d="M2 11a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-7Zm7.5 1a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5Z"
                    clip-rule="evenodd" />
                <path d="M10.5 14.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z" />
            </svg>
            Pagar pendiente
        </button>
    </div>

    <x-action-message on='action-message-venta'>
        @if (session('fail'))
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
    <!--Modal pagos -->
    <x-modal name="modal-pagos" title="Agregar metodo de pago">
        <x-slot name='body'>
            @include('livewire.puntos.ventas.nueva.include.modal-pagos-body')
        </x-slot>
    </x-modal>
    <!--INDICADOR DE CARGA, DE VENTA-->
    <div wire:loading.delay.long wire:target='pagarVentaPendiente'>
        <x-loading-screen name='loading'>
            <x-slot name='body'>
                <div class="flex">
                    <div class="me-4">
                        @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
                    </div>
                    <p>Procesando venta pendiente</p>
                </div>
            </x-slot>
        </x-loading-screen>
    </div>
    <!--Script para cerrar la ventana-->
    @include('livewire.puntos.ventas.include.close-pendiente')
    <!--Script para imprimir el ticket-->
    @include('livewire.puntos.ventas.include.print-script')
</div>
