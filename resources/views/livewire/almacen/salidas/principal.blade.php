<div>
    {{-- Buscar por fecha --}}
    <div class="relative ms-3 w-40">
        <label for="name" class="block mb-2 text-base font-medium text-gray-900 dark:text-white">Buscar por
            día:</label>
        <input type="date" id="fecha"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
    </div>

    {{-- Tabla --}}
    <div class="ms-3 mx-3 my-3">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            FOLIO
                        </th>
                        <th scope="col" class="px-6 py-3">
                            FECHA DE SALIDA
                        </th>
                        <th scope="col" class="px-6 py-3">
                            ORIGEN
                        </th>
                        <th scope="col" class="px-6 py-3">
                            DESTINO
                        </th>
                        <th scope="col" class="px-6 py-3 w-96">
                            NOTA
                        </th>
                        <th scope="col" class="px-6 py-3">
                            ACCIONES
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($listaSalidas as $salidas)
                        <tr wire:key={{ $salidas->folio }}
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4">
                                {{ $salidas->folio }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $salidas->fecha }}
                            </td>
                            <td class="px-6 py-4 uppercase">
                                {{ $salidas->origen }}
                            </td>
                            <td class="px-6 py-4 uppercase">
                                {{ $salidas->destino }}
                            </td>
                            <td class="px-6 py-4 uppercase">
                                {{ $salidas->observaciones }}
                            </td>
                            <td class="px-6 py-4">
                                <button x-data x-on:click="$dispatch('open-modal', { name: 'detallesSalida' })"
                                    class="text-green-700 hover:text-white border border-green-700 hover:bg-green-800 inline-flex items-center focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center mb-2 dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800">
                                    <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-width="2"
                                            d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z" />
                                        <path stroke="currentColor" stroke-width="2"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    Detalles
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal para ver los detalles de una salida --}}
    <x-modal name="detallesSalida" title="DETALLES DE SALIDA">
        {{-- MODAL BODY --}}
        <x-slot:body>
            <div class="space-y-4">
                
            </div>
        </x-slot>
    </x-modal>
</div>
