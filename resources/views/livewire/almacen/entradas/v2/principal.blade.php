<div>
    {{-- Buscar por fecha --}}
    <form class="flex gap-4 items-end ms-3 w-40" method="GET" wire:submit='buscar'>
        @csrf
        <div>
            <label for="name" class="block mb-1 text-base font-medium text-gray-900 dark:text-white">
                Buscar por mes:</label>
            <input type="month" wire:model='mes_busqueda'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
        </div>
        <div>
            <button type="submit" wire:click='buscar'
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
    </form>

    {{-- Tabla con información --}}
    <div class="ms-3 mx-3 ">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-2">
                            FOLIO
                        </th>
                        <th scope="col" class="px-6 py-2">
                            REQUISICION
                        </th>
                        <th scope="col" class="px-6 py-2">
                            FECHA EXISTENCIAS
                        </th>
                        <th scope="col" class="px-6 py-2">
                            BODEGA
                        </th>
                        <th scope="col" class="px-6 py-2">
                            OBSERVACIONES
                        </th>
                        <th scope="col" class="px-6 py-2">
                            SUBTOTAL
                        </th>
                        <th scope="col" class="px-6 py-3">
                            IVA
                        </th>
                        <th scope="col" class="px-6 py-3">
                            TOTAL
                        </th>
                        <th scope="col" class="px-6 py-3">
                            ACCIONES
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->entradas as $index => $entrada)
                        <tr wire:key='{{ $entrada->folio }}'
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-2">
                                {{ $entrada->folio }}
                            </td>
                            <td class="px-6 py-2">
                                {{ $entrada->folio_requisicion }}
                            </td>
                            <td class="px-6 py-2">
                                {{ $entrada->fecha_existencias }}
                            </td>
                            <td class="px-6 py-2">
                                {{ $entrada->bodega->descripcion }}
                            </td>
                            <td class="px-6 py-2  max-w-64">
                                {{ $entrada->observaciones }}
                            </td>
                            <td class="px-6 py-2">
                                ${{ number_format($entrada->subtotal, 2) }}
                            </td>
                            <td class="px-6 py-2">
                                ${{ number_format($entrada->iva, 2) }}
                            </td>
                            <td class="px-6 py-2">
                                ${{ number_format($entrada->total, 2) }}
                            </td>
                            <td class="px-6 py-2 flex ">
                                {{-- EDITAR --}}
                                <a href="{{ route('almacen.entradav2.editar', ['folio' => $entrada->folio]) }}"
                                    class="px-3.5 py-1.5 text-green-700 border border-green-700 hover:bg-green-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:focus:ring-green-800 dark:hover:bg-green-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24"
                                        fill="currentColor" width="24" height="24">
                                        <path fill-rule="evenodd"
                                            d="M11.32 6.176H5c-1.105 0-2 .949-2 2.118v10.588C3 20.052 3.895 21 5 21h11c1.105 0 2-.948 2-2.118v-7.75l-3.914 4.144A2.46 2.46 0 0 1 12.81 16l-2.681.568c-1.75.37-3.292-1.263-2.942-3.115l.536-2.839c.097-.512.335-.983.684-1.352l2.914-3.086Z"
                                            clip-rule="evenodd" />
                                        <path fill-rule="evenodd"
                                            d="M19.846 4.318a2.148 2.148 0 0 0-.437-.692 2.014 2.014 0 0 0-.654-.463 1.92 1.92 0 0 0-1.544 0 2.014 2.014 0 0 0-.654.463l-.546.578 2.852 3.02.546-.579a2.14 2.14 0 0 0 .437-.692 2.244 2.244 0 0 0 0-1.635ZM17.45 8.721 14.597 5.7 9.82 10.76a.54.54 0 0 0-.137.27l-.536 2.84c-.07.37.239.696.588.622l2.682-.567a.492.492 0 0 0 .255-.145l4.778-5.06Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </a>
                                {{-- Detalles --}}
                                <a type="button" wire:click="verDetalles({{ $entrada->folio }})"
                                    class="w-auto text-gray-700 hover:text-white border border-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-3 py-1.5 text-center dark:border-gray-500 dark:text-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-800">
                                    <div wire:loading.remove.delay wire:target='verDetalles({{ $entrada->folio }})'>
                                        <svg class="w-5 h-5" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd"
                                                d="M2 6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6Zm4.996 2a1 1 0 0 0 0 2h.01a1 1 0 1 0 0-2h-.01ZM11 8a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2h-6Zm-4.004 3a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2h-.01ZM11 11a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2h-6Zm-4.004 3a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2h-.01ZM11 14a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2h-6Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <!--Loading indicator-->
                                    <div wire:loading.delay wire:target='verDetalles({{ $entrada->folio }})'>
                                        @include('livewire.utils.loading', ['w' => 5, 'h' => 5])
                                    </div>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div>
            {{ $this->entradas->links() }}
        </div>
    </div>

    {{-- MODAL DE INFORMACION DE ENTRADA --}}
    <x-modal name="modal-entrada" title="Detalles de entrada">
        <x-slot name='body'>
            <p>FOLIO ENTRADA: {{ $entrada_seleccionada }}</p>
            <div class="relative max-h-96 overflow-y-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-2">
                                Presentacion
                            </th>
                            <th scope="col" class="px-6 py-2">
                                Cantidad
                            </th>
                            <th scope="col" class="px-6 py-2">
                                C.Unitario
                            </th>
                            <th scope="col" class="px-6 py-2">
                                Iva
                            </th>
                            <th scope="col" class="px-6 py-2">
                                C.C.Impuesto
                            </th>
                            <th scope="col" class="px-6 py-2">
                                Importe
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($entrada_detalles as $index => $detalle)
                            <tr wire:key='{{ $index }}'
                                class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                <th scope="row"
                                    class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $detalle->descripcion }}
                                </th>
                                <td class="px-6 py-2">
                                    {{ $detalle->cantidad }}
                                </td>
                                <td class="px-6 py-2">
                                    ${{ $detalle->costo_unitario }}
                                </td>
                                <td class="px-6 py-2">
                                    {{ $detalle->iva }}
                                </td>
                                <td class="px-6 py-2">
                                    ${{ $detalle->costo_con_impuesto }}
                                </td>
                                <td class="px-6 py-2">
                                    ${{ $detalle->importe }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-slot>
    </x-modal>
</div>
