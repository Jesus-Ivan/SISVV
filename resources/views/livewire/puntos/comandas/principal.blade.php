<div>
    {{-- Barra de acciones --}}
    <form method="GET" wire:submit='buscar'>
        @csrf
        <div class="flex gap-2 mx-2">
            {{-- DropDown Punto Venta --}}
            <div>
                <button id="dropdownCheckboxButton" data-dropdown-toggle="dropdownDefaultCheckbox"
                    class="text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                    type="button">Punto Venta<svg class="w-2.5 h-2.5 ms-3" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 4 4 4-4" />
                    </svg>
                </button>

                <!-- Dropdown menu -->
                <div id="dropdownDefaultCheckbox"
                    class="z-10 hidden w-48 bg-white divide-y divide-gray-100 rounded-lg shadow-sm dark:bg-gray-700 dark:divide-gray-600">
                    <ul class="p-3 space-y-3 text-sm text-gray-700 dark:text-gray-200"
                        aria-labelledby="dropdownCheckboxButton">
                        @foreach ($this->puntos as $p)
                            <li wire:key='{{ $p->clave }}'>
                                <div class="flex items-center">
                                    <input id="checkbox-{{ $p->clave }}" type="checkbox"
                                        wire:model="selected_puntos.{{ $p->clave }}"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                    <label for="checkbox-{{ $p->clave }}"
                                        class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300 w-full">{{ $p->nombre }}</label>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            {{-- Barra busqueda --}}
            <div class=" grow">
                <div class="w-80">
                    <label for="default-search"
                        class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                        <input wire:model="search" type="text"
                            class="w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Nombre o numero de socio" />
                    </div>
                </div>
            </div>
            {{-- FECHA --}}
            <div>
                <input type="date" wire:model='fecha'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            </div>
            {{-- BOTON BUSQUEDA --}}
            <div>
                <button type="submit" wire:click='buscar'
                    class="w-32 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                    <div wire:loading wire:target='buscar' class="me-4">
                        @include('livewire.utils.loading', ['w' => 4, 'h' => 4])
                    </div>
                    Buscar
                </button>
            </div>
        </div>
    </form>
    {{-- TABLA DE COMANDAS --}}
    <div class="relative shadow-md sm:rounded-lg mx-3">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-3 py-2">
                        #
                    </th>
                    <th scope="col" class="px-3 py-2">
                        PRODUCTO
                    </th>
                    <th scope="col" class="px-3 py-2">
                        FECHA
                    </th>
                    <th scope="col" class="px-3 py-2">
                        SOCIO
                    </th>
                    <th scope="col" class="px-3 py-2">
                        ESTADO
                    </th>
                    <th scope="col" class="px-3 py-2">
                        <span class="sr-only">ACCIONES</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->productos as $i => $prod)
                    <tr wire:key='{{ $i }}'
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <th scope="row"
                            class=" text-xl px-3 py-1 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $prod->cantidad }}
                        </th>
                        <td class="px-3 py-1">
                            <p class="font-bold">
                                {{ $prod->nombre }}
                            </p>
                            <p>{{ $prod->observaciones }}</p>
                        </td>
                        <td class="px-3 py-1">
                            {{ $prod->inicio }}
                        </td>
                        <td class="px-3 py-1">
                            {{ $prod->venta->nombre }}
                        </td>
                        <td class="px-3 py-1">
                            @include('livewire.puntos.comandas.include.estado')
                        </td>
                        <td class="px-3 py-1 text-right">
                            <a href="{{ route('comandas.ticket', ['folio' => $prod->folio_venta, 'inicio' => $prod->inicio]) }}"
                                target="_blank"
                                class="text-green-700 border border-green-700 hover:bg-green-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:focus:ring-green-800 dark:hover:bg-green-500">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="w-5 h-5">
                                    <path fill-rule="evenodd"
                                        d="M7.875 1.5C6.839 1.5 6 2.34 6 3.375v2.99c-.426.053-.851.11-1.274.174-1.454.218-2.476 1.483-2.476 2.917v6.294a3 3 0 0 0 3 3h.27l-.155 1.705A1.875 1.875 0 0 0 7.232 22.5h9.536a1.875 1.875 0 0 0 1.867-2.045l-.155-1.705h.27a3 3 0 0 0 3-3V9.456c0-1.434-1.022-2.7-2.476-2.917A48.716 48.716 0 0 0 18 6.366V3.375c0-1.036-.84-1.875-1.875-1.875h-8.25ZM16.5 6.205v-2.83A.375.375 0 0 0 16.125 3h-8.25a.375.375 0 0 0-.375.375v2.83a49.353 49.353 0 0 1 9 0Zm-.217 8.265c.178.018.317.16.333.337l.526 5.784a.375.375 0 0 1-.374.409H7.232a.375.375 0 0 1-.374-.409l.526-5.784a.373.373 0 0 1 .333-.337 41.741 41.741 0 0 1 8.566 0Zm.967-3.97a.75.75 0 0 1 .75-.75h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75H18a.75.75 0 0 1-.75-.75V10.5ZM15 9.75a.75.75 0 0 0-.75.75v.008c0 .414.336.75.75.75h.008a.75.75 0 0 0 .75-.75V10.5a.75.75 0 0 0-.75-.75H15Z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="sr-only">Reimpresion</span>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{-- NOTIFICACION en tiempo real (alphine js) --}}
    <x-notification />
</div>
<script>
    document.addEventListener('livewire:init', () => {

        let audio_error = new Audio('/sounds/error-notification.mp3');
        let audio_success = new Audio('/sounds/bell-notification.mp3');
        let audio_standard = new Audio('/sounds/notification.mp3');

        Livewire.on('play-error-sound', (event) => {
            audio_error.play().catch(e => console.log("Bloqueado por el navegador"));
            // 2. Disparar Toast (esto lo atrapa el @notify.window de Alpine)
            window.dispatchEvent(new CustomEvent('notify', {
                detail: {
                    message: 'ERROR DE IMPRESION',
                    type: 'error'
                }
            }));
        });

        Livewire.on('play-success-sound', (event) => {
            audio_success.play().catch(e => console.log("Bloqueado por el navegador"));
            // 2. Disparar Toast (esto lo atrapa el @notify.window de Alpine)
            window.dispatchEvent(new CustomEvent('notify', {
                detail: {
                    message: 'COMANDA LISTA!!',
                    type: 'success'
                }
            }));
        });
    });
</script>
