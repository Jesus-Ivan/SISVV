<div>
    {{-- Search input --}}
    <div class="pb-4 w-full bg-white dark:bg-gray-900">
        <label for="table-search" class="sr-only">Descripcion</label>
        <div class="relative mt-1">
            <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                <svg wire:loading.remove.delay wire:target='ventaForm.seachVenta'
                    class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                </svg>
                <!--Loading indicator-->
                <div wire:loading.delay.long wire:target='ventaForm.seachVenta'>
                    @include('livewire.utils.loading', ['w' => 5, 'h' => 5])
                </div>
            </div>
            <input type="text" wire:model.live.debounce.700ms ="ventaForm.seachVenta"
                class="w-full block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Nombre o No. de socio">
        </div>
    </div>
    {{-- Info del producto --}}
    @if (!is_null($ventaForm->indexTransferible))
        <div class="flex gap-2">
            <p class="font-bold">Producto:
            <p class="grow">
                {{ $ventaForm->productosTable[$ventaForm->indexTransferible]['catalogo_productos']['nombre'] }}
            </p>
            </p>
            <p class="font-bold">Cantidad:
            <p>
                {{ $ventaForm->productosTable[$ventaForm->indexTransferible]['cantidad'] }}
            </p>
            </p>
        </div>
    @endif
    {{-- Table --}}
    <div class="overflow-y-auto h-96  shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Folio
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Nombre
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Total
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Accion
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->ventas_abiertas as $index => $venta)
                    <tr wire:key='{{ $index }}'
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                        <th scope="row"
                            class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $venta->folio }}
                        </th>
                        <td class="px-6 py-2">
                            <div>
                                {{ $venta->nombre }}
                            </div>
                            <div>
                                {{ $venta->id_socio }}
                            </div>
                        </td>
                        <td class="px-6 py-2">
                            ${{ $venta->total }}
                        </td>
                        <td class="px-6 py-2">
                            <a wire:click='confirmarMovimiento({{ $venta->folio }})'
                                class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Mover</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
