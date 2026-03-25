<div x-data="{
    selectedIds: [],
    visible_modif: false,
    message_modif: '',
    confirmar() { $wire.confirmarOrdenes(this.selectedIds); },
    close_modif() {
        this.visible_modif = false;
        this.message_modif = '';
    },
    open_modif(message) {
        this.visible_modif = true;
        this.message_modif = message;
    }
}" x-on:clear-ids.window="
        selectedIds = [];
    "
    x-on:show-modif.window="open_modif($event.detail[0])">
    <div x-show="visible_modif" x-transition x-cloak>
        <div class="p-2 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded-xl">
            <button class="w-full" x-on:click='close_modif'>
                <span class=" flex align-middle text-center font-semibold">
                    <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd"
                            d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm7.707-3.707a1 1 0 0 0-1.414 1.414L10.586 12l-2.293 2.293a1 1 0 1 0 1.414 1.414L12 13.414l2.293 2.293a1 1 0 0 0 1.414-1.414L13.414 12l2.293-2.293a1 1 0 0 0-1.414-1.414L12 10.586 9.707 8.293Z"
                            clip-rule="evenodd" />
                    </svg>
                    <span x-text="message_modif"></span>
                </span>
            </button>
        </div>
    </div>
    {{-- SEARCH BAR --}}
    <form class="flex" method="GET" wire:submit='buscar'>
        @csrf
        <div class="flex gap-4 w-full">
            <input type="date" wire:model='fecha_busqueda'
                class="w-fit bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            <button type="submit" wire:click='buscar'
                class="text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                <svg wire:loading.remove wire:target='buscar' class="w-5 h-5" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                        d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                </svg>
                <!--Loading indicator-->
                <div wire:loading wire:target='buscar'>
                    @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
                </div>
                <span class="sr-only">Buscar</span>
            </button>
        </div>
        <span>
            <button type="button" x-on:click="confirmar"
                class="w-60 justify-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                Confirmar ordenes
            </button>
        </span>
    </form>
    {{-- CARDS --}}
    <div class="flex overflow-x-auto gap-3 my-2">
        <!--CARD DE ORDEN-->
        @foreach ($this->ordenes as $key => $orden)
            <div x-data="{
                selectAll: false,
                selectedItems: [],
                allItems: {{ json_encode(array_column($orden['detalles'], 'id')) }},
                toggleAll() {
                    this.selectedItems = this.selectAll ? [] : [...this.allItems];
                    if (this.selectAll) {
                        aux = selectedIds.filter((id) => !this.allItems.includes(id));
                        selectedIds = aux;
                    } else {
                        aux = this.allItems.filter((id) => !selectedIds.includes(id));
                        selectedIds = [...selectedIds, ...aux];
                    }
                },
                updateSelectAllState() {
                    this.selectAll = this.selectedItems.length === this.allItems.length;
                },
                updateId(id) {
                    if (selectedIds.includes(id))
                        selectedIds = selectedIds.filter((id_aux) => id != id_aux);
                    else
                        selectedIds = [...selectedIds, parseInt(id)];
                },
                uncheck(ids) {
                    selectedIds = [];
                    this.selectedItems = [];
                },
            }"
                x-on:comandas-confirmadas.window="
                uncheck($event.detail[0]);
                updateSelectAllState();
            "
                wire:key='{{ $key }}'
                class="p-3 w-fit min-h-96 bg-white    border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                <!--Header-->
                <div>
                    <div class="flex justify-between items-center">
                        <div class="flex gap-1 items-end">
                            <p class="font-normal text-gray-700 dark:text-gray-400">{{ $orden['venta']['folio'] }}</p>
                        </div>
                        <x-dropdown>
                            <x-slot name="trigger">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                        viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-width="4"
                                            d="M12 6h.01M12 12h.01M12 18h.01" />
                                    </svg>
                                    <p class="font-bold text-gray-700 dark:text-gray-400">
                                        {{ $orden['venta']['punto_venta']['nombre'] }}
                                    </p>
                                </div>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link
                                    wire:click="reimprimirComanda( {{ $orden['venta']['folio'] }}, '{{ $orden['inicio'] }}')">
                                    Reimprimir
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                        <p class="font-normal text-gray-700 dark:text-gray-400">{{ substr($orden['inicio'], 0, 16) }}
                        </p>
                    </div>
                    <div class="flex gap-1 items-end">
                        <h6 class="font-bold dark:text-white">Cliente:</h6>
                        <p class="font-normal text-gray-700 dark:text-gray-400">{{ $orden['venta']['nombre'] }}</p>
                    </div>
                </div>
                <!--Table-->
                <div>
                    <table class="w-96 text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="p-2">
                                    #
                                </th>
                                <th scope="col" class="p-2">
                                    Platillo
                                </th>
                                <th scope="col" class="p-2">
                                    <input id="main-check" type="checkbox" x-model="selectAll" value="!selectAll"
                                        x-on:click='toggleAll'
                                        class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orden['detalles'] as $i => $item)
                                <tr wire:key='p.{{ $key }}.{{ $i }}'
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <th scope="row"
                                        class="w-10 p-2 font-medium text-lg text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $item['cantidad'] }}
                                    </th>
                                    <td class="p-2 {{ $item['id_estado'] == $estado_error ? 'text-red-500' : '' }}">
                                        <label for="checkbox.{{ $key }}.{{ $i }}"
                                            x-on:click="updateId({{ $item['id'] }})"
                                            class="{{ $item['id_estado'] == $estado_listo ? 'line-through' : '' }}">
                                            <p class="font-bold">{{ $item['nombre'] }}</p>
                                            <p>{{ $item['observaciones'] }}</p>
                                        </label>
                                    </td>
                                    <td class="p-2 w-10">
                                        <input id="checkbox.{{ $key }}.{{ $i }}" type="checkbox"
                                            name="selected_items[]" value="{{ $item['id'] }}" x-model="selectedItems"
                                            x-on:change="updateSelectAllState"
                                            class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
    {{-- NOTIFICACION en tiempo real (alphine js) --}}
    <x-notification />
    {{-- PAGINATOR --}}
    <div>
        {{ $this->ordenes->links() }}
    </div>
    {{-- INDICADOR DE CARGA --}}
    <div wire:loading wire:target='confirmarOrdenes'>
        <x-loading-screen name='loading'>
            <x-slot name='body'>
                <div class="flex">
                    <div class="me-4">
                        @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
                    </div>
                    <p>Actualizando comandas</p>
                </div>
            </x-slot>
        </x-loading-screen>
    </div>
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

        Livewire.on('play-standard-sound', (event) => {
            audio_standard.play().catch(e => console.log("Bloqueado por el navegador"));
            // 2. Disparar Toast (esto lo atrapa el @notify.window de Alpine)
            window.dispatchEvent(new CustomEvent('notify', {
                detail: {
                    message: 'NUEVA COMANDA!!',
                    type: 'success'
                }
            }));
        });
    });
</script>
