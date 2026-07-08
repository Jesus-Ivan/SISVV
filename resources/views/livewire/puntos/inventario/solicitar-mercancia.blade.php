<div class="ms-3 mx-3">
    {{-- FECHA MES --}}
    {{-- FECHA MES --}}
    <div class="flex gap-4 items-end my-3">
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Buscar por Mes</label>
            <input datepicker type="month" wire:model.live='search_mes'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        </div>
        <div>
            <!--Loading indicator-->
            <div wire:loading wire:target='search_mes'>
                @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
            </div>
        </div>
    </div>

    {{-- TABLA DE SOLICITUDES --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg my-2">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        FOLIO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        USUARIO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        FECHA
                    </th>
                    <th scope="col" class="px-6 py-3">
                        APLICADO
                    </th>
                    <th scope="col" class="px-6 py-3">

                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->pedidos as $detalles)
                    <tr wire:key='{{ $detalles->folio }}'
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                        <th scope="row"
                            class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $detalles->folio }}
                        </th>
                        <td class="px-3 py-2">
                            {{ $detalles->user_name }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $detalles->fecha_existencias }}
                        </td>
                        <td class="px-3 py-2">
                            @if ($detalles->folio_traspaso == 0)
                                <span
                                    class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                    PENDIENTE
                                </span>
                            @else
                                <span
                                    class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                    FOLIO: {{ $detalles->folio_traspaso }}
                                </span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-center">
                            <button type="button" wire:click='detallesPedido({{ $detalles->folio }})'
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-3 py-2  dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                <div wire:loading.remove.delay wire:target='detallesPedido({{ $detalles->folio }})'>
                                    Detalles
                                </div>
                                <!--Loading indicator-->
                                <div class="flex justify-center" wire:loading.delay
                                    wire:target='detallesPedido({{ $detalles->folio }})'>
                                    @include('livewire.utils.loading', ['w' => 4, 'h' => 4])
                                </div>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $this->pedidos->links() }}
    </div>

    {{-- Modal para ver los detalles de la factura --}}
    <x-modal name="detalles" title="DETALLES DE SOLICITUD">
        <x-slot:body>
            <p>FOLIO SOLICITUD: {{ $pedido_seleccionado }}</p>
            {{-- Detalles de la factura --}}
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg" style="max-height: 300px; overflow-y: auto;">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-3 py-2">
                                CLAVE INSUMO
                            </th>
                            <th scope="col" class="px-3 py-2">
                                DESCRIPCIÓN
                            </th>
                            <th scope="col" class="px-3 py-2">
                                EXISTENCIA
                            </th>
                            <th scope="col" class="px-3 py-2">
                                CANTIDAD SOLICITADA
                            </th>
                    </thead>
                    <tbody>
                        @foreach ($detalles_pedido as $index => $detalle)
                            <tr wire:key='{{ $index }}'
                                class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                <th scope="row"
                                    class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $detalle->clave_insumo }}
                                </th>
                                <td class="px-3 py-2 w-96">
                                    {{ $detalle->descripcion }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ $detalle->existencias }}
                                </td>
                                <td class="px-3 py-2 text-center">
                                    {{ $detalle->cantidad_insumo }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-slot>
    </x-modal>
</div>
