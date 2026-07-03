<div class="ms-3 mx-3">
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


    {{-- TABLA --}}
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
                        PUNTO VENTA
                    </th>
                    <th scope="col" class="px-6 py-3">
                        FECHA
                    </th>
                    <th scope="col" class="px-6 py-3">
                        APLICADO
                    </th>
                    <th scope="col" class="px-6 py-3 text-center">
                        ACCIONES
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
                            {{ $detalles->bodegaOrigen->descripcion }}
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
                            @if ($detalles->folio_traspaso == 0)
                                {{-- Si el folio de traspaso esta pendiente, puede realizar el traspaso --}}
                                <a type="button"
                                    href="{{ route('almacen.traspasov2.nuevo', ['folio_pedido' => $detalles->folio]) }}"
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-3 py-2  dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M18 14v4.833A1.166 1.166 0 0 1 16.833 20H5.167A1.167 1.167 0 0 1 4 18.833V7.167A1.166 1.166 0 0 1 5.167 6h4.618m4.447-2H20v5.768m-7.889 2.121 7.778-7.778" />
                                    </svg>
                                </a>
                            @else
                                {{-- Muestra un botón gris deshabilitado si ya tiene un folio --}}
                                <button disabled type="button"
                                    class="text-white bg-gray-400 cursor-not-allowed font-medium rounded-lg text-sm px-3 py-2 dark:bg-gray-600">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $this->pedidos->links() }}
    </div>
</div>
