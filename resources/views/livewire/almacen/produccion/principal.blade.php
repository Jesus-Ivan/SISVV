<div>
    {{-- Buscar por fecha --}}
    <div class="flex gap-4 items-end ms-3 w-40">
        <div>
            <label for="name" class="block mb-1 text-base font-medium text-gray-900 dark:text-white">
                Buscar por mes:</label>
            <input type="month" wire:model='mes_busqueda'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
        </div>
        <div>
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
        </div>
    </div>

    {{-- Tabla resultados --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="w-24 px-3 py-2">
                        FOLIO
                    </th>
                    <th scope="col" class="px-3 py-2">
                        FECHA EXISTENCIAS
                    </th>
                    <th scope="col" class="px-3 py-2">
                        ORIGEN
                    </th>
                    <th scope="col" class="px-3 py-2">
                        DESTINO
                    </th>
                    <th scope="col" class="px-3 py-2">
                        OBSERVACIONES
                    </th>
                    <th scope="col" class="px-3 py-2">
                        ACCIONES
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->producciones as $i => $produccion)
                    <tr wire:key='{{ $produccion->folio }}'
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                        <th scope="row"
                            class="w-24 px-3 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $produccion->folio }}
                        </th>
                        <td class="px-3 py-2">
                            {{ $produccion->fecha_existencias }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $produccion->origen->descripcion }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $produccion->destino->descripcion }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $produccion->observaciones }}
                        </td>
                        <td class="px-3 py-2">
                            <div class="flex">
                                <div>
                                    <button type="button" wire:click='verDetalles({{ $produccion->folio }})'
                                        class="text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-1.5 text-center inline-flex items-blue dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500">
                                        <div wire:loading.remove wire:target='verDetalles({{ $produccion->folio }})'
                                            class="flex">
                                            Detalles
                                        </div>
                                        <!--Loading indicator-->
                                        <div wire:loading wire:target='verDetalles({{ $produccion->folio }})'>
                                            @include('livewire.utils.loading', ['w' => 5, 'h' => 5])
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div>
        {{ $this->producciones->links() }}
    </div>

    {{-- MODAL DE INFORMACION DE PRODUCCION --}}
    <x-modal name="modal-produccion" title="Detalles de entrada: {{ $folio }}">
        <x-slot name='body'>
            <div class="relative max-h-96 overflow-y-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-2">
                                INSUMO ELABORADO
                            </th>
                            <th scope="col" class="px-6 py-2">
                                CANTIDAD
                            </th>
                            <th scope="col" class="px-6 py-2">
                                RENDIMIENTO
                            </th>
                            <th scope="col" class="px-6 py-2">
                                TOTAL
                            </th>
                            <th scope="col" class="px-6 py-2">
                                ORIGEN
                            </th>
                            <th scope="col" class="px-6 py-2">
                                DESTINO
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->detalles_produccion as $index => $detalle)
                            <tr wire:key='{{ $index }}'
                                class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                <th scope="row"
                                    class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $detalle->insumoElaborado->descripcion }}
                                </th>
                                <td class="px-6 py-2">
                                    {{ $detalle->cantidad }}
                                </td>
                                <td class="px-6 py-2">
                                    {{ $detalle->rendimiento }}
                                </td>
                                <td class="px-6 py-2">
                                    {{ $detalle->total_elaborado }}
                                </td>
                                <td class="px-6 py-2">
                                    {{ $detalle->transformacion?->origen->descripcion }}
                                </td>
                                <td class="px-6 py-2">
                                    {{ $detalle->transformacion?->destino->descripcion }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-slot>
    </x-modal>
</div>
