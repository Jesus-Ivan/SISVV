<div class="ms-3 mx-3">
    {{-- BUSQUEDA POR FECHA --}}
    <div class="flex gap-4 items-end">
        <div>
            <label class="block mb-1 text-base font-medium text-gray-900 dark:text-white">Buscar por Mes</label>
            <input type="month" wire:model='search_mes'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        </div>
        <div>
            <button type="submit" wire:click='buscar'
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                <svg wire:loading.remove wire:target='buscar' class="w-5 h-5" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                        d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                </svg>
                <!--Loading indicator-->
                <div wire:loading wire:target='buscar'>
                    @include('livewire.utils.loading', ['w' => 5, 'h' => 5])
                </div>
                <span class="sr-only">Buscar</span>
            </button>
        </div>
    </div>

    {{-- TABLA DE RESULTADOS --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg my-2">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-3 py-3">
                        FOLIO
                    </th>
                    <th scope="col" class="px-3 py-3">
                        REQUISICIÓN
                    </th>
                    <th scope="col" class="px-3 py-3">
                        FECHA EXISTENCIAS
                    </th>
                    <th scope="col" class="px-3 py-3">
                        RESPONSABLE
                    </th>
                    <th scope="col" class="px-3 py-3">
                        ORIGEN
                    </th>
                    <th scope="col" class="px-3 py-3">
                        DESTINO
                    </th>
                    <th scope="col" class="px-3 py-3">
                        OBSERVACIONES
                    </th>
                    <th scope="col" class="px-3 py-3">

                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->traspasos as $index => $traspaso)
                    <tr wire:key='{{ $traspaso->folio }}'
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                        <th scope="row"
                            class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $traspaso->folio }}
                        </th>
                        <td class="px-3 py-2">
                            {{ $traspaso->folio_requisicion ?? 'N/A' }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $traspaso->fecha_existencias }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $traspaso->nombre }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $traspaso->origen->descripcion }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $traspaso->destino->descripcion }}
                        </td>
                        <td class="px-3 py-2 w-96">
                            {{ $traspaso->observaciones }}
                        </td>
                        <td class="px-3 py-2 text-center">
                            <button type="button" wire:click='detallesTraspaso({{ $traspaso->folio }})'
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-3 py-2  dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                <div wire:loading.remove.delay wire:target='detallesTraspaso({{ $traspaso->folio }})'>
                                    Detalles
                                </div>
                                <!--Loading indicator-->
                                <div class="flex justify-center" wire:loading.delay
                                    wire:target='detallesTraspaso({{ $traspaso->folio }})'>
                                    @include('livewire.utils.loading', ['w' => 4, 'h' => 4])
                                </div>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div>
        {{ $this->traspasos->links() }}
    </div>

    {{-- Modal para ver los detalles de la factura --}}
    <x-modal name="modal-traspaso" title="DETALLES DE TRASPASO">
        <x-slot:body>
            <p>FOLIO TRASPASO: {{ $traspaso_seleccionado }}</p>
            {{-- Detalles de la factura --}}
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg" style="max-height: 300px; overflow-y: auto;">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                CLAVE PRESENTACIÓN
                            </th>
                            <th scope="col" class="px-6 py-3">
                                CLAVE INSUMO
                            </th>
                            <th scope="col" class="px-6 py-3">
                                DESCRIPCIÓN
                            </th>
                            <th scope="col" class="px-6 py-3">
                                CANTIDAD
                            </th>
                            <th scope="col" class="px-6 py-3">
                                CANTIDAD INSUMO
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($traspaso_detalles as $index => $detalle)
                            <tr wire:key='{{ $index }}'
                                class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $detalle->clave_presentacion ?? 'N/A' }}
                                </th>
                                <td class="px-6 py-4">
                                    {{ $detalle->clave_insumo ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 w-96">
                                    {{ $detalle->descripcion }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $detalle->cantidad }}
                                </td>
                                <td class="px-6 py-4">
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
