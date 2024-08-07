<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    NO. SOCIO
                </th>
                <th scope="col" class="px-6 py-3">
                    METODO DE PAGO
                </th>
                <th scope="col" class="px-6 py-3">
                    SUBTOTAL
                </th>
                <th scope="col" class="px-6 py-3">
                    PROPINA
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    ACCIONES
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($this->ventaForm->pagosTable as $pagoIndex => $pago)
                <tr wire:key='{{ $pagoIndex }}'
                    class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $pago['id_socio'] }}
                    </th>
                    <td class="px-6 py-4">
                        {{ $pago['descripcion_tipo_pago'] }}
                    </td>
                    <td class="px-6 py-4">
                        ${{ $pago['monto_pago'] }}
                    </td>
                    <td class="px-6 py-4">
                        ${{ $pago['propina'] }}
                    </td>
                    <td class="px-6 py-4">
                        <button type="button" wire:click="eliminarPago({{ $pagoIndex }})"
                            class="text-red-700 border border-red-700 hover:bg-red-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:focus:ring-red-800 dark:hover:bg-red-500">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-5 h-5">
                                <path fill-rule="evenodd"
                                    d="M8.586 2.586A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4a2 2 0 0 1 .586-1.414ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="sr-only">Borrar</span>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="font-semibold text-gray-900 dark:text-white">
                <th scope="row" class="px-6 py-3 text-base">
                    {{-- <p>SubTotal</p>
                    <p>Descuento</p> --}}
                    <p>Total</p>
                </th>
                <td class="px-6 py-3"></td>
                <td class="px-6 py-3"></td>
                <td class="px-6 py-3">
                    {{-- <p>$</p>
                    <p>$</p> --}}
                    <p>${{ $this->ventaForm->totalPago }}</p>
                </td>
            </tr>
        </tfoot>
    </table>
    @error('ventaForm.pagosTable')
        <x-input-error messages="{{ $message }}" />
    @enderror
</div>
