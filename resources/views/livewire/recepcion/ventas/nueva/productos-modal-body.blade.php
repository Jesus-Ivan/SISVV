<div>
    <div class="max-w-2xl" wire:loading.class='pointer-events-none' wire:target='finishSelect'>
        <!-- Modal body -->
        <div class="shadow-md sm:rounded-lg">
            <div class="flex gap-3" x-trap="show">
                <!-- Search bar-->
                <div class="pb-4 w-full bg-white dark:bg-gray-900">
                    <label for="table-search" class="sr-only">Descripcion</label>
                    <div class="relative mt-1">
                        <div
                            class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg wire:loading.remove.delay.long wire:target='searchProduct'
                                class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                            <!--Loading indicator-->
                            <div wire:loading.delay.long wire:target='searchProduct'>
                                @include('livewire.utils.loading', ['w' => 5, 'h' => 5])
                            </div>
                        </div>
                        <input type="text" wire:model.live.debounce.700ms ="searchProduct"
                            class="w-full block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Descripcion">
                    </div>
                </div>
                {{-- Cantidad --}}
                <div>
                    <input type="number" wire:model="cantidadProducto"
                        class="w-full mt-1 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Cantidad">
                    @error('cantidadProducto')
                        <x-input-error messages="{{ $message }}" />
                    @enderror
                    @if (session('fail_cantidad'))
                        <x-input-error messages="{{ session('fail_cantidad') }}" />
                    @endif
                </div>
            </div>
            <!-- Result table-->
            <div class="overflow-y-auto h-96">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                CODIGO
                            </th>
                            <th scope="col" class="py-3">
                                DESCRIPCION
                            </th>
                            <th scope="col" class="px-6 py-3">
                                COSTO
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->productosNew as $producto)
                            <tr wire:key="{{ $producto->clave }}"
                                wire:click='seleccionarProducto({{ $producto->clave }})'
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $producto->clave }}
                                </th>
                                <td class="w-full font-medium text-gray-900  dark:text-white">
                                    <button class=" w-full py-4 text-left">
                                        {{ $producto->descripcion }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    ${{ $producto->precio_con_impuestos }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
