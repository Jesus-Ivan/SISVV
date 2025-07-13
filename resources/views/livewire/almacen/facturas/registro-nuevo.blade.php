<div class="ms-3 mx-3" @keyup.ctrl.window="$dispatch('open-modal', {name:'agregar-presentacion'})">
    <div class="flex justify-between items-end">
        <div class="flex gap-2 items-end">
            {{-- BARRA DE BUSQUEDA --}}
            <form class="w-fit" wire:submit='' method="GET">
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input type="search" wire:model='folio_entrada'
                        class="block w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Folio Entrada" />
                </div>
            </form>
            {{-- PROVEEDOR --}}
            <form class="w-fit">
                <select id="proveedor" wire:model='id_proveedor'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected value="{{ null }}">PROVEEDOR</option>
                    @foreach ($this->proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                    @endforeach
                </select>
                @error('id_proveedor')
                    <x-input-error messages="{{ $message }}" />
                @enderror
            </form>
            {{-- FORMA DE PAGO --}}
            <form class="w-fit">
                <select id="tipo_compra" wire:model='cuenta_contable'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected value="{{ null }}">TIPO DE COMPRA</option>
                    @foreach ($metodo_pago as $index => $item)
                        <option value="{{ $item }}">{{ $item }}</option>
                    @endforeach
                </select>
                @error('cuenta_contable')
                    <x-input-error messages="{{ $message }}" />
                @enderror
            </form>
        </div>

        <div class="flex gap-8 items-end">
            {{-- FECHA FACTURA --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Fecha Factura (Nota)</label>
                <input datepicker type="date" wire:model='fecha_compra'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-36 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                @error('folio')
                    <x-input-error messages="{{ $message }}" />
                @enderror
            </div>
            {{-- FECHA VENCIMIENTO --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Fecha Vencimiento</label>
                <input datepicker type="date" wire:model='fecha_vencimiento'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-36 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            </div>
        </div>
    </div>

    <div class="flex justify-between items-end my-2">
        <div class="flex w-52 items-end">
            {{-- OBSERVACIONES --}}
            <input type="text" id="referencia" wire:model='observaciones'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Observaciones" />

        </div>
        <div class="flex w-80 justify-between">
            {{-- FOLIO FACTURA --}}
            <input type="text" id="folio" wire:model='folio_remision'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Folio Factura o Nota" />

        </div>
    </div>

    {{-- TABLA DE REGISTRO DE FACTURAS --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg my-2" style="max-height: 400px; overflow-y: auto;">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        #
                    </th>
                    <th scope="col" class="px-6 py-3">
                        PRESENTACIÓN
                    </th>
                    <th scope="col" class="px-6 py-3">
                        CANTIDAD
                    </th>
                    <th scope="col" class="px-6 py-3">
                        COSTO SIN IVA
                    </th>
                    <th scope="col" class="px-6 py-3">
                        IVA
                    </th>
                    <th scope="col" class="px-6 py-3">
                        COSTO CON IVA
                    </th>
                    <th scope="col" class="px-6 py-3">
                        IMPORTE
                    </th>
                    <th scope="col" class="px-6 py-3">

                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($listaPresentaciones as $index => $presentacion)
                    <tr
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                        <th scope="row"
                            class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $presentacion['clave'] }}
                        </th>
                        <td class="px-6 py-2 w-96">
                            {{ $presentacion['descripcion'] }}
                        </td>
                        <td class="px-6 py-2">
                            <input wire:model.live="listaPresentaciones.{{ $index }}.cantidad"
                                wire:change='importeActualizado({{ $index }})' type="number" min="0"
                                class="w-24 max-w-20 block p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </td>
                        <td class="px-6 py-2 flex gap-2 items-center">
                            $ <input wire:model.live="listaPresentaciones.{{ $index }}.costo"
                                wire:change='costoIvaActualizado({{ $index }})' type="number" min="0"
                                class="w-24 max-w-20 block p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </td>
                        <td class="px-6 py-2">
                            <input wire:model.live="listaPresentaciones.{{ $index }}.iva"
                                wire:change='costoIvaActualizado({{ $index }})' type="number" min="0"
                                class="w-14 max-w-20 block p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </td>
                        <td class="px-6 py-2 flex gap-2 items-center">
                            $ <input wire:model.live="listaPresentaciones.{{ $index }}.costo_con_impuesto"
                                wire:change='costoSinIvaActualizado({{ $index }})' type="number"
                                min="0"
                                class="w-40 max-w-20 block p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </td>
                        <td class="px-6 py-2">
                            $ {{ $presentacion['importe'] }}
                        </td>
                        <td class="px-6 py-2 text-center">
                            <button type="button" wire:click="removePresentacion('{{ $index }}')"
                                class="text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-3.5 py-2 text-center me-2 mb-2 dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="w-4 h-4">
                                    <path fill-rule="evenodd"
                                        d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- BOTON DE CANCELAR --}}
    <a type="button" href="{{ route('almacen.facturas') }}"
        class="my-2 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
        <svg class="w-5 h-5 me-2 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
            width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M6 18 17.94 6M18 18 6.06 6" />
        </svg>
        Cancelar
    </a>
    {{-- BOTON DE GUARDAR --}}
    <button type="button" wire:click='crearFactura()'
        class="my-2 text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
        <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
            height="24" fill="currentColor" viewBox="0 0 24 24">
            <path fill-rule="evenodd"
                d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm10 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM8 7V5h8v2a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1Z"
                clip-rule="evenodd" />
        </svg>
        Guardar
    </button>

    {{-- MODAL PARA AGREGAR PRESENTACION --}}
    @include('livewire.almacen.facturas.modal-agregar')

    <!--Alerts-->
    <x-action-message on='open-action-message'>
        @if (session('success'))
            <div id="alert-exito"
                class="flex items-center p-4 mb-4 text-green-800 border-t-4 border-green-300 bg-green-50 dark:text-green-400 dark:bg-gray-800 dark:border-green-800"
                role="alert">
                <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <div class="ms-3 text-sm font-medium">
                    {{ session('success') }}
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
