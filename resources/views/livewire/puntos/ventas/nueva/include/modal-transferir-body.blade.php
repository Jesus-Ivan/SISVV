<div x-data="{
    ventas_abiertas: [],
    ventas_mostradas: [],
    buscar(e) {
        let input = e.target.value; //Obtener el valor de entrada (busqueda)
        //Filtrar los resultados originales, y asignarlos temporalmente
        this.ventas_mostradas = this.ventas_abiertas.filter(venta => venta.id_socio == input || venta.nombre.toLowerCase().includes(input.toLowerCase()));
    },
    confirmarTransferencia(params) {
        let ventas = this.ventas_abiertas.filter(venta => venta.cantidad_destino > 0);
        $wire.confirmarTransferencia(ventas);
    }
}"
    x-on:ventas-abiertas.window="    
        ventas_abiertas = $event.detail.ventas_abiertas;
        ventas_mostradas = $event.detail.ventas_abiertas;
    ">
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
            <input type="text" x-on:input.debounce.500ms="buscar"
                class="w-full block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Nombre o No. de socio">
        </div>
    </div>
    {{-- Info del producto --}}
    @if (!is_null($ventaForm->indexSeleccionado))
        <div class="flex gap-2">
            <p class="font-bold">Producto:
            <p class="grow">
                {{ $ventaForm->productosTable[$ventaForm->indexSeleccionado]['nombre'] }}
            </p>
            </p>
            <p class="font-bold">Cantidad:
            <p>
                {{ $ventaForm->productosTable[$ventaForm->indexSeleccionado]['cantidad'] }}
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
                        DESTINO
                    </th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(v, i) in ventas_mostradas" :key="i">
                    <tr
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                        <th scope="row" x-text="v.folio"
                            class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        </th>
                        <td class="px-6 py-2">
                            <div>
                                <p x-text="v.nombre" class="font-semibold" />
                            </div>
                            <div>
                                <p x-text="v.id_socio" class="font-semibold" />
                            </div>
                        </td>
                        <td class="px-6 py-2">
                            <p x-text="v.total" />
                        </td>
                        <td class="px-6 py-2">
                            <input type="number" min="0" max="300" step="1"
                                x-model="v.cantidad_destino"
                                class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        <!-- Modal footer -->
        <div class="flex items-center border-t border-gray-200 rounded-b dark:border-gray-600">
            <button type="button" x-on:click="confirmarTransferencia()"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                <div wire:loading.delay wire:target='agregarPago' class="me-4">
                    @include('livewire.utils.loading', ['w' => 4, 'h' => 4])
                </div>
                Aceptar
            </button>
        </div>
    </div>

</div>
