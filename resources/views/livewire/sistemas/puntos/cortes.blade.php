<div class="ms-3 mx-3">
    <div class="flex gap-4 items-end">
        {{-- FECHA --}}
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Fecha Inicio:</label>
            <input datepicker type="date" wire:model.live='fInicio'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        </div>
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Fecha Fin:</label>
            <input datepicker type="date" wire:model.live='fFin'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        </div>
        <div>
            <label for="usuarios" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Usuario:</label>
            <select id="usuarios" wire:model.live='id_usuario'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option selected value="{{ null }}">SELECCIONAR</option>
                @foreach ($this->usuarios as $usuario)
                    <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <!--Loading indicator-->
            <div wire:loading wire:target='fInicio, fFin, id_usuario'>
                @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
            </div>
        </div>
    </div>

    {{-- TABLA DE RESULTADOS --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg my-2">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        CORTE
                    </th>
                    <th scope="col" class="px-6 py-3">
                        PUNTO VENTA
                    </th>
                    <th scope="col" class="px-6 py-3">
                        FECHA APERTURA
                    </th>
                    <th scope="col" class="px-6 py-3">
                        FECHA CIERRE
                    </th>
                    <th scope="col" class="px-6 py-3">
                        USUARIO
                    </th>
                    <th scope="col" class="px-6 py-3">

                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->cortesPuntos as $corte)
                    <tr wire:key='{{ $corte->corte }}'
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                        <th scope="row"
                            class="px-6 py-1 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $corte->corte }}
                        </th>
                        <td class="px-6 py-1">
                            {{ $corte->puntoVenta->nombre }}
                        </td>
                        <td class="px-6 py-1">
                            {{ $corte->fecha_apertura }}
                        </td>
                        <td class="px-6 py-1">
                            {{ $corte->fecha_cierre ?? 'PUNTO ABIERTO' }}
                        </td>
                        <td class="px-6 py-1">
                            {{ $corte->users->name }}
                        </td>
                        <td class="px-6 py-1 text-center">
                            <a href="{{ route('ventas.corte', ['caja' => $corte->corte, 'codigopv' => $corte->clave_punto_venta]) }}"
                                target="_blank"
                                class="text-green-700 border border-green-700 hover:bg-green-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:focus:ring-green-800 dark:hover:bg-green-500">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="w-5 h-5">
                                    <path fill-rule="evenodd"
                                        d="M7.875 1.5C6.839 1.5 6 2.34 6 3.375v2.99c-.426.053-.851.11-1.274.174-1.454.218-2.476 1.483-2.476 2.917v6.294a3 3 0 0 0 3 3h.27l-.155 1.705A1.875 1.875 0 0 0 7.232 22.5h9.536a1.875 1.875 0 0 0 1.867-2.045l-.155-1.705h.27a3 3 0 0 0 3-3V9.456c0-1.434-1.022-2.7-2.476-2.917A48.716 48.716 0 0 0 18 6.366V3.375c0-1.036-.84-1.875-1.875-1.875h-8.25ZM16.5 6.205v-2.83A.375.375 0 0 0 16.125 3h-8.25a.375.375 0 0 0-.375.375v2.83a49.353 49.353 0 0 1 9 0Zm-.217 8.265c.178.018.317.16.333.337l.526 5.784a.375.375 0 0 1-.374.409H7.232a.375.375 0 0 1-.374-.409l.526-5.784a.373.373 0 0 1 .333-.337 41.741 41.741 0 0 1 8.566 0Zm.967-3.97a.75.75 0 0 1 .75-.75h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75H18a.75.75 0 0 1-.75-.75V10.5ZM15 9.75a.75.75 0 0 0-.75.75v.008c0 .414.336.75.75.75h.008a.75.75 0 0 0 .75-.75V10.5a.75.75 0 0 0-.75-.75H15Z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="sr-only">Imprimir</span>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $this->cortesPuntos->links() }}
    </div>
</div>
