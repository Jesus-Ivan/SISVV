<div>
    <form class="flex m-2 items-end gap-4">
        {{-- Fecha --}}
        <div class="flex gap-4 grow">
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
    <div class="shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        FOLIO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        SOCIO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        FECHA
                    </th>
                    <th scope="col" class="px-6 py-3">
                        TOTAL
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cobros as $index => $cobro)
                    <tr wire:key="{{ $index }}"
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                        <th scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $cobro->folio }}
                        </th>
                        <td class="px-6 py-4">
                            {{ $cobro->nombre }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $cobro->created_at }}
                        </td>
                        <td class="px-6 py-4">
                            ${{ $cobro->total }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div>
            @if ($caja)
                {{ $cobros->links() }}
            @endif
        </div>
    </div>
    <!--Botones de navegacion (regresar y imprimir reporte)-->
    <div>
        <a type="button" href="{{ route('recepcion.cobros') }}"
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
            <a type="button" href="{{ route('recepcion.cobros.corte-detalles', ['caja' => $caja[0]->corte]) }}"
                target="_blank"
                class="inline-flex items-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                <svg class="w-6 h-6 dark:text-gray-800 text-white me-2" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                    viewBox="0 0 24 24">
                    <path fill-rule="evenodd"
                        d="M8 3a2 2 0 0 0-2 2v3h12V5a2 2 0 0 0-2-2H8Zm-3 7a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h1v-4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v4h1a2 2 0 0 0 2-2v-5a2 2 0 0 0-2-2H5Zm4 11a1 1 0 0 1-1-1v-4h8v4a1 1 0 0 1-1 1H9Z"
                        clip-rule="evenodd" />
                </svg>
                Imprimir reporte - detallado
            </a>

            <a type="button" href="{{ route('recepcion.cobros.corte-resumen', ['caja' => $caja[0]->corte]) }}"
                target="_blank"
                class="inline-flex items-center text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-600 dark:hover:bg-gray-700 focus:outline-none dark:focus:ring-gray-800">
                <svg class="w-6 h-6 dark:text-gray-800 text-white me-2" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                    viewBox="0 0 24 24">
                    <path fill-rule="evenodd"
                        d="M8 3a2 2 0 0 0-2 2v3h12V5a2 2 0 0 0-2-2H8Zm-3 7a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h1v-4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v4h1a2 2 0 0 0 2-2v-5a2 2 0 0 0-2-2H5Zm4 11a1 1 0 0 1-1-1v-4h8v4a1 1 0 0 1-1 1H9Z"
                        clip-rule="evenodd" />
                </svg>
                Imprimir reporte - resumen
            </a>
        @endif
    </div>
</div>
