<div class="p-2">
    {{-- Search bar --}}
    <div class="flex py-3 gap-3">
        <div>
            {{-- Select de bodega --}}
            <select id="bodega" wire:model='clave_bodega' id="disabled-input" aria-label="disabled input" disabled
                class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-fit p-1.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option selected value="{{ null }}">BODEGA</option>
                @foreach ($this->bodegas as $b)
                    <option value="{{ $b->clave }}">{{ $b->descripcion }}</option>
                @endforeach
            </select>
            @error('clave_bodega')
                <x-input-error messages="{{ $message }}" />
            @enderror
        </div>
        <div>
            <input type="date" wire:model='fecha' disabled aria-label="disabled input"
                class="cursor-not-allowed bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="0.0" />
            @error('fecha')
                <x-input-error messages="{{ $message }}" />
            @enderror
        </div>
        <div>
            <input type="time" wire:model='hora' disabled aria-label="disabled input"
                class="cursor-not-allowed bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="0.0" />
            @error('hora')
                <x-input-error messages="{{ $message }}" />
            @enderror
        </div>
        {{-- proveedor general --}}
        <div>
            <select wire:model.live='proveedor'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="{{ null }}" selected>Proveedor...</option>
                @foreach ($this->proveedores as $p)
                    <option wire:key='{{ $p->id }}' value="{{ $p->id }}">{{ $p->nombre }}</option>
                @endforeach
            </select>
        </div>
        {{-- cuenta contable general --}}
        <div>
            <select wire:model.live='cuenta'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="{{ null }}" selected>C.Contable</option>
                @foreach ($cuentas as $c_contable)
                    <option value="{{ $c_contable }}">{{ $c_contable }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex grow justify-end">
            <input type="text" wire:model='observaciones'
                class="h-9 max-w-md bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Observaciones ..." />
        </div>

    </div>
    {{-- Tabla de resultados --}}
    <div class="relative overflow-y-auto shadow-md sm:rounded-lg h-96">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-3 py-2">
                        #
                    </th>
                    <th scope="col" class="px-3 py-2">
                        Descripcion
                    </th>
                    <th scope="col" class="px-3 py-2">
                        Proveedor
                    </th>
                    <th scope="col" class="px-3 py-2">
                        F.FACTURA
                    </th>
                    <th scope="col" class="px-3 py-2">
                        C.CONTABLE
                    </th>
                    <th scope="col" class="px-3 py-2">
                        Cantidad
                    </th>
                    <th scope="col" class="px-3 py-2">
                        Unidad
                    </th>
                    <th scope="col" class="px-3 py-2">
                        C.Unitario
                    </th>
                    <th scope="col" class="px-3 py-2">
                        IVA
                    </th>
                    <th scope="col" class="px-3 py-2">
                        C.C.Impuesto
                    </th>
                    <th scope="col" class="px-3 py-2">
                        Subtotal
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($articulos_table as $index => $item)
                    <tr wire:key='{{ $index }}'
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <th scope="row"
                            class="w-fit px-3 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $item['clave'] }}
                        </th>
                        <td class="px-3 py-2">
                            {{ $item['descripcion'] }}
                        </td>
                        <td class="px-3 py-2">
                            <select wire:model='articulos_table.{{ $index }}.id_proveedor'
                                class="w-fit bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option selected value="{{ null }}">Seleccione</option>
                                @foreach ($this->proveedores as $i => $p)
                                    <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-3 py-2">
                            <input type="text" wire:model='articulos_table.{{ $index }}.factura'
                                class="max-w-20 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block  p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="N/D" />
                        </td>
                        <td class="px-3 py-2">
                            <select wire:model='articulos_table.{{ $index }}.cuenta_contable'
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="{{ null }}" selected>C.Contable</option>
                                @foreach ($cuentas as $c_contable)
                                    <option value="{{ $c_contable }}">{{ $c_contable }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-3 py-2">
                            {{ $item['cantidad'] }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $item['unidad'] ? $item['unidad']['descripcion'] : '' }}
                        </td>
                        <td class="px-3 py-2 flex items-center">
                            $
                            <input type="number" wire:model='articulos_table.{{ $index }}.costo'
                                wire:change='updateCostoIva({{ $index }})'
                                class="max-w-20 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block  p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="0.0" />
                        </td>
                        <td class="px-3 py-2">
                            <input type="number" wire:model='articulos_table.{{ $index }}.iva'
                                wire:change='updateCostoIva({{ $index }})'
                                class="max-w-16 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block  p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="0.0" />
                        </td>
                        <td class="px-3 py-2 flex items-center">
                            $
                            <input type="number" wire:model='articulos_table.{{ $index }}.costo_con_impuesto'
                                wire:change='updateCostoSinIva({{ $index }})'
                                class="max-w-20 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block  p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="0.0" />
                        </td>
                        <td class="px-3 py-2">
                            ${{ number_format($item['importe'], 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- BOTONES DE ACCION --}}
    <div>

        <button type="button" wire:click='actualizarEntrada'
            class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Actualizar
            Entrada
        </button>
        <a type="button" href="{{ route('almacen.entradav2') }}"
            class="py-2.5 px-5 ms-3 text-sm font-medium text-red-900 focus:outline-none bg-red rounded-lg border border-red-200 hover:bg-red-100 hover:text-red-700 focus:z-10 focus:ring-4 focus:ring-red-100 dark:focus:ring-red-700 dark:bg-red-800 dark:text-red-400 dark:border-red-600 dark:hover:text-white dark:hover:bg-red-700">Cancelar
        </a>
    </div>
    {{-- LOADING SCREEN --}}
    <div wire:loading.delay wire:target='actualizarEntrada'>
        <x-loading-screen>
            <x-slot name='body'>
                <div class="flex">
                    <div class="me-4">
                        @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
                    </div>
                    <p>Guardando entrada...</p>
                </div>
            </x-slot>
        </x-loading-screen>
    </div>
    {{-- Action message --}}
    <x-action-message on='entrada'>
        @if (session('success-entrada'))
            <div id="alert-exito"
                class="flex items-center p-4 mb-4 text-green-800 border-t-4 border-green-300 bg-green-50 dark:text-green-400 dark:bg-gray-800 dark:border-green-800"
                role="alert">
                <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <div class="ms-3 text-sm font-medium">
                    {{ session('success-entrada') }}
                </div>
            </div>
        @else
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
</div>
