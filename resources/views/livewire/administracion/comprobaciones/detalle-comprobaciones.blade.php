<div class="ms-3 mx-3">
    <div class="flex gap-4 items-end">
        {{-- BUSCAR POR AREA --}}
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Buscar Área</label>
            <select wire:model.live="search_area"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">SELECCIONAR</option>
                @foreach ($this->areas as $area)
                    <option value="{{ $area }}">{{ $area }}</option>
                @endforeach
            </select>
        </div>
        {{-- BUSCAR POR CONCEPTO --}}
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Buscar Concepto</label>
            <select wire:model.live="search_concepto"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">SELECCIONAR</option>
                @foreach ($this->conceptos as $con)
                    <option value="{{ $con }}">{{ $con }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <!--Loading indicator-->
            <div wire:loading wire:target='search_area, search_concepto'>
                @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
            </div>
        </div>
    </div>
    {{-- TABLA DE DETALLES --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg my-2 h-96">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        FECHA NOTA
                    </th>
                    <th scope="col" class="px-3 py-3">
                        TIPO DOCUMENTO
                    </th>
                    <th scope="col" class="px-3 py-3">
                        PROVEEDOR
                    </th>
                    <th scope="col" class="px-3 py-3">
                        AREA
                    </th>
                    <th scope="col" class="px-3 py-3">
                        CONCEPTO
                    </th>
                    <th scope="col" class="px-3 py-3">
                        IMPORTE
                    </th>
                    <th scope="col" class="px-3 py-3">
                        FORMA DE PAGO
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->detallesC as $detalles)
                    <tr wire:key='{{ $detalles->id }}'
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                        <th scope="row"
                            class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $detalles->fecha_nota }}
                        </th>
                        <td class="px-3 py-2">
                            {{ $detalles->tipo_documento }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $detalles->proveedor }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $detalles->area }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $detalles->concepto }}
                        </td>
                        <td class="px-3 py-2">
                            $ {{ number_format($detalles->importe, 2) }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $detalles->forma_pago }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{-- BOTON DE REGRESAR --}}
    <a type="button" href="{{ route('administracion.ver-comp') }}"
        class="my-2 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
        <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
            width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M5 12h14M5 12l4-4m-4 4 4 4" />
        </svg>

        Regresar
    </a>
</div>
