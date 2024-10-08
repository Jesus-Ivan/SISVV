<div>
    <form class="flex m-2 items-end gap-4">
        <div class="flex gap-4 grow">
            <!--Fecha-->
            <div class="w-48">
                <label for="fecha" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fecha</label>
                <input type="date" id="tel-celular" wire:model="fecha"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                @error('fecha')
                    <x-input-error messages="{{ $message }}" />
                @enderror
            </div>
        </div>
        <!--Boton de busqueda -->
        <button type="button" wire:click='buscar'
            class="flex text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">
            <div wire:loading.delay wire:target='buscar' class="me-4">
                @include('livewire.utils.loading', ['w' => 5, 'h' => 5])
            </div>
            Buscar
        </button>
    </form>
    <!--Tabla de ventas -->
    <div class="h-96 relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        FOLIO VENTA
                    </th>
                    <th scope="col" class="px-6 py-3">
                        NO.SOCIO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        NOMBRE
                    </th>
                    <th scope="col" class="px-6 py-3">
                        FECHA
                    </th>
                    <th scope="col" class="px-6 py-3">
                        TOTAL
                    </th>
                    <th scope="col" class="px-6 py-3">
                        CORTE DE CAJA
                    </th>
                    <th scope="col" class="px-6 py-3">
                        ESTADO
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ventas as $venta)
                    <tr
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                        <th scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $venta->folio }}
                        </th>
                        <td class="px-6 py-4">
                            {{ $venta->id_socio }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $venta->nombre }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $venta->fecha_apertura }}
                        </td>
                        <td class="px-6 py-4">
                            ${{ $venta->total }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $venta->corte_caja }}
                        </td>
                        <td class="px-6 py-4">
                            @if ($venta->fecha_cierre)
                                <span
                                    class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Cerrada
                                </span>
                            @else
                                <span
                                    class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">Abierta
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div>
        @if ($caja)
            <div>{{ $ventas->links() }}</div>
        @endif
    </div>
    <!--Botones de navegacion (regresar y imprimir reporte)-->
    <div>
        <a type="button" href="{{ route('pv.ventas', ['codigopv' => $codigopv]) }}"
            class="inline-flex items-center focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">
            <svg class="w-6 h-6 dark:text-gray-800 text-white me-2" aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                viewBox="0 0 24 24">
                <path
                    d="M14.502 7.046h-2.5v-.928a2.122 2.122 0 0 0-1.199-1.954 1.827 1.827 0 0 0-1.984.311L3.71 8.965a2.2 2.2 0 0 0 0 3.24L8.82 16.7a1.829 1.829 0 0 0 1.985.31 2.121 2.121 0 0 0 1.199-1.959v-.928h1a2.025 2.025 0 0 1 1.999 2.047V19a1 1 0 0 0 1.275.961 6.59 6.59 0 0 0 4.662-7.22 6.593 6.593 0 0 0-6.437-5.695Z" />
            </svg>
            Regresar
        </a>
        @if ($caja)
            <a type="button" href="{{ route('ventas.corte', ['caja' => $caja->corte, 'codigopv' => $codigopv]) }}"
                target="_blank"
                class="inline-flex items-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                <svg class="w-6 h-6 dark:text-gray-800 text-white me-2" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                    viewBox="0 0 24 24">
                    <path fill-rule="evenodd"
                        d="M8 3a2 2 0 0 0-2 2v3h12V5a2 2 0 0 0-2-2H8Zm-3 7a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h1v-4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v4h1a2 2 0 0 0 2-2v-5a2 2 0 0 0-2-2H5Zm4 11a1 1 0 0 1-1-1v-4h8v4a1 1 0 0 1-1 1H9Z"
                        clip-rule="evenodd" />
                </svg>
                Imprimir reporte
            </a>
            {{--
            @if ($caja->fecha_cierre)
            @else
                <button type="button" x-data x-on:click="$dispatch('open-modal', {name:'modalAdvertencia'})"
                    class="inline-flex items-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                    <svg class="w-6 h-6 dark:text-gray-800 text-white me-2" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                        viewBox="0 0 24 24">
                        <path fill-rule="evenodd"
                            d="M8 3a2 2 0 0 0-2 2v3h12V5a2 2 0 0 0-2-2H8Zm-3 7a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h1v-4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v4h1a2 2 0 0 0 2-2v-5a2 2 0 0 0-2-2H5Zm4 11a1 1 0 0 1-1-1v-4h8v4a1 1 0 0 1-1 1H9Z"
                            clip-rule="evenodd" />
                    </svg>
                    Imprimir reporte
                </button>
            @endif
            --}}
        @endif
    </div>
    {{-- Modal de Advertencia --}}
    <x-modal title="Cierra tu caja" name="modalAdvertencia">
        <x-slot name='body'>
            <div class="text-center">
                <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <h3 class="mb-5 text-xl font-normal text-gray-500 dark:text-gray-400">¡¡ Advertencia !!
                </h3>
                <p class="text-gray-500 dark:text-gray-400">
                    Para imprimir tu reporte de ventas, primero debes cerrar totalmente tu caja.
                </p>
            </div>
        </x-slot>
        <x-slot name='footer'>
            <a type="button" href="{{ route('pv.caja', ['codigopv' => $codigopv]) }}"
                class="text-white bg-green-600 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                Continuar
            </a>
        </x-slot>
    </x-modal>
</div>
