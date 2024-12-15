<div>
    {{-- Buscar por fecha --}}
    <div class="flex gap-2 items-end">
        <div class="relative ms-3 w-40">
            <label for="name" class="block mb-2 text-base font-medium text-gray-900 dark:text-white">Buscar por
                mes:</label>
            <input type="date" id="fecha" wire:model = "fSearch"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
        </div>
        <div>
            <button wire:click='buscar'
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
        </div>
    </div>

    {{-- Tabla --}}
    <div class="ms-3 mx-3 my-3">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-2">
                            FOLIO
                        </th>
                        <th scope="col" class="px-6 py-2">
                            FECHA DE SALIDA
                        </th>
                        <th scope="col" class="px-6 py-2">
                            ORIGEN
                        </th>
                        <th scope="col" class="px-6 py-2">
                            DESTINO
                        </th>
                        <th scope="col" class="px-6 py-2 w-96">
                            Observaciones
                        </th>
                        <th scope="col" class="px-6 py-2">
                            Monto
                        </th>
                        <th scope="col" class="px-6 py-2">
                            ACCIONES
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->salidas as $index => $salida)
                        <tr wire:key={{ $salida->folio }}
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-2">
                                {{ $salida->folio }}
                            </td>
                            <td class="px-6 py-2">
                                {{ $salida->fecha }}
                            </td>
                            <td class="px-6 py-2 uppercase">
                                {{ $salida->bodegaOrigen->descripcion }}
                            </td>
                            <td class="px-6 py-2 uppercase">
                                {{ $salida->destino->descripcion }}
                            </td>
                            <td class="px-6 py-2 uppercase">
                                {{ $salida->observaciones }}
                            </td>
                            <td class="px-6 py-2 uppercase">
                                $ {{ $salida->monto }}
                            </td>
                            <td class="px-6 py-2">
                                <a wire:click="verDetalles({{ $salida->folio }})"
                                    class="w-20 text-gray-700 hover:text-white border border-gray-700 hover:bg-gray-800 inline-flex focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-3 py-1.5 text-center dark:border-gray-500 dark:text-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-800">
                                    <div wire:loading.remove.delay wire:target='verDetalles({{ $salida->folio }})'>
                                        Detalles
                                    </div>
                                    <!--Loading indicator-->
                                    <div class="flex justify-center"   wire:loading.delay wire:target='verDetalles({{ $salida->folio }})'>
                                        @include('livewire.utils.loading', ['w' => 5, 'h' => 5])
                                    </div>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div>
        {{ $this->salidas->links() }}
    </div>

    {{-- Modal para ver los detalles de una salida --}}
    <x-modal name="modal-salida" title="DETALLES DE SALIDA">
        {{-- MODAL BODY --}}
        <x-slot:body>
            <div class="space-y-4">
                <p>FOLIO SALIDA: {{ $salida_seleccionada }}</p>
                <div class="relative max-h-96 overflow-y-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-2">
                                    Producto
                                </th>
                                <th scope="col" class="px-6 py-2">
                                    Cantidad origen
                                </th>
                                <th scope="col" class="px-6 py-2">
                                    Peso origen
                                </th>
                                <th scope="col" class="px-6 py-2">
                                    Cantidad salida
                                </th>
                                <th scope="col" class="px-6 py-2">
                                    Peso salida
                                </th>
                                <th scope="col" class="px-6 py-2">
                                    Monto
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($salida_detalles as $index => $detalle)
                                <tr wire:key='{{ $index }}'
                                    class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                    <th scope="row"
                                        class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $detalle->nombre }}
                                    </th>
                                    <td class="px-6 py-2">
                                        {{ $detalle->stock_origen_cantidad }}
                                    </td>
                                    <td class="px-6 py-2">
                                        {{ $detalle->stock_origen_peso }}
                                    </td>
                                    <td class="px-6 py-2">
                                        {{ $detalle->cantidad_salida }}
                                    </td>
                                    <td class="px-6 py-2">
                                        {{ $detalle->peso_salida }}
                                    </td>
                                    <td class="px-6 py-2">
                                        $ {{ $detalle->monto }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </x-slot>
    </x-modal>
</div>
