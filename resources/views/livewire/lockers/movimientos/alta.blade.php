@use('App\Constants\LockersConstants')
<div x-data="{
    locker_extraordinario: false,
    tipo_selected: $wire.tipo_selected,
    open_image: false,
    selectedImage: '',
    selectedName: ''
}">
    {{-- SOCIO O CASO EXECPCIONAL --}}
    <div>
        {{-- BARRA BUSQUEDA --}}
        <div class="flex gap-2">
            <div class="flex gap-2 w-full" x-show="!locker_extraordinario">
                {{-- Integrantes / socios select --}}
                <div>
                    <select x-model='tipo_selected'
                        class="w-fit bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        @foreach ($tipo as $i => $item)
                            <option wire:key='{{ $i }}' value="{{ $item }}">{{ $item }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- Componente de busqueda de socios o integrantes --}}
                <div class="w-full">
                    {{-- Socios --}}
                    <div x-show="tipo_selected == '{{ LockersConstants::KEY_SOCIO }}'">
                        <livewire:search-bar tittle="Buscar socio" table="socios" :columns="['id', 'nombre', 'apellido_p', 'apellido_m']" primary="id"
                            event="on-selected-socio" :conditions="[['deleted_at', '=', null]]" />
                    </div>
                    {{-- INTEGRANTES --}}
                    <div x-show="tipo_selected == '{{ LockersConstants::KEY_INTEGRANTE }}'" x-cloakhuh>
                        <livewire:search-bar tittle="Buscar integrantes" table="integrantes_socios" :columns="['id_socio', 'nombre_integrante', 'apellido_p_integrante', 'apellido_m_integrante']"
                            primary="id" event="on-selected-integrante" :conditions="[['deleted_at', '=', null]]" />
                    </div>
                </div>
            </div>

            <div class="flex gap-2 w-full" x-show="locker_extraordinario" x-cloak>
                {{-- SECCION --}}
                <select wire:model="seccion_general"
                    class="w-fit bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected value="">SECCIÓN</option>
                    @foreach ($secciones as $k => $seccion)
                        <option value="{{ $k }}">{{ $seccion }}</option>
                    @endforeach
                </select>

                {{-- iNPUT BAR --}}
                <div class="w-full">
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
                        <input type="number" wire:model='input_search' wire:keyup.enter='buscar'
                            class="block w-full p-3 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="No. Locker..." />
                        <button wire:click='buscar'
                            class="text-white absolute end-2 bottom-2 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-1.5 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Buscar</button>
                    </div>
                </div>
            </div>
            {{-- checkbox excepcion --}}
            <div class="flex">
                <div class="flex items-center h-5">
                    <input id="helper-checkbox" aria-describedby="helper-checkbox-text" type="checkbox"
                        x-model='locker_extraordinario' x-on:click="locker_extraordinario = !locker_extraordinario"
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                </div>
                <div class="ms-2 text-sm w-48">
                    <label for="helper-checkbox" class="font-medium text-gray-900 dark:text-gray-300">Permitir
                        excepción</label>
                    <p id="helper-checkbox-text" class="text-xs font-normal text-gray-500 dark:text-gray-300">Locker sin
                        numero de socio</p>
                </div>
            </div>
        </div>
        {{-- LISTA DE INTEGRANTES/SOCIO Y LISTA CUOTAS --}}
        <div class="grid grid-cols-2 gap-2 p-3" x-show="!locker_extraordinario">
            <div>
                <ul class="max-w-md divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($miembros as $i => $m)
                        <li class="py-2  hover:bg-purple-200 cursor-pointer" wire:key='{{ $i }}'
                            x-on:click="selectedImage = '{{ asset($m['img_path']) }}'; selectedName = '{{ $m['nombre'] }}'; open_image = true">
                            <div class="flex items-center space-x-4 rtl:space-x-reverse">
                                <div class="shrink-0">
                                    <img class="w-10 h-10 rounded-full" src="{{ asset($m['img_path']) }}"
                                        alt="integrante">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                        {{ implode(' ', [$m['nombre'], $m['apellido_p'], $m['apellido_m']]) }}
                                    </p>
                                    <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                        {{ $m['parentesco'] ?: 'ACCION: ' . $m['id_socio'] }}
                                    </p>
                                </div>
                                <div
                                    class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                    @if ($m['parentesco'] == null && $m['id_integrante'] == null)
                                        <span
                                            class="bg-purple-100 text-purple-800 text-sm font-medium  px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-purple-400 border border-purple-400">Titular</span>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @empty
                        <p>Lista no disponible.</p>
                    @endforelse
                </ul>
            </div>
            {{-- LISTA CUOTAS --}}
            <div>
                <p class="font-bold">Lockers disponibles Sistema</p>
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    Seccion
                                </th>
                                <th scope="col" class="px-6 py-3 w-32">
                                    # locker
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Titular / Integrante
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Observaciones
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lockers_sistema as $l_index => $l_cuota)
                                <tr wire:key='{{ $l_index }}'
                                    class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                    <td class="px-6 py-4">
                                        <select wire:model='lockers_sistema.{{ $l_index }}.seccion'
                                            class="w-20 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            <option selected value="">Seleccione</option>
                                            @foreach ($secciones as $k => $seccion)
                                                <option value="{{ $k }}">{{ $seccion }}</option>
                                            @endforeach
                                        </select>
                                        @error('lockers_sistema.' . $l_index . '.seccion')
                                            <x-input-error messages="{{ $message }}" />
                                        @enderror
                                    </td>
                                    <td class="px-6 py-4 ">
                                        <input type="number" wire:model='lockers_sistema.{{ $l_index }}.numero'
                                            min="1" max="300" step="1"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        @error('lockers_sistema.' . $l_index . '.numero')
                                            <x-input-error messages="{{ $message }}" />
                                        @enderror
                                    </td>
                                    <td class="px-6 py-4">
                                        <select wire:model='lockers_sistema.{{ $l_index }}.index_miembro'
                                            class="w-fit bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            <option selected value="">Seleccione</option>
                                            @foreach ($miembros as $index => $m)
                                                <option value="{{ $index }}">
                                                    {{ $m['nombre'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('lockers_sistema.' . $l_index . '.index_miembro')
                                            <x-input-error messages="{{ $message }}" />
                                        @enderror
                                    </td>
                                    <td class="px-6 py-4 ">
                                        <input type="text"
                                            wire:model='lockers_sistema.{{ $l_index }}.observaciones'
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!--Linea -->
    <hr class="h-px my-4 bg-gray-300 border-0 dark:bg-gray-700">
    {{-- CASO EXEPCIONAL --}}
    <div x-show="locker_extraordinario" x-cloak>
        <div class="mx-3">
            <p class="font-bold">Lockers extradorinarios</p>
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                #LOCKER
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Seccion
                            </th>
                            <th scope="col" class="px-6 py-3 w-32">
                                ESTADO ACTUAL
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Observaciones
                            </th>
                            <th scope="col" class="px-6 py-3">
                                ACCIONES
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lockers_excep as $i => $locker)
                            <tr wire:key='{{ $i }}'
                                class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $locker['numero'] }}
                                </th>
                                <td class="px-6 py-4">
                                    {{ $locker['seccion'] }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $locker['estado_actual'] }}
                                </td>
                                <td class="px-6 py-4 ">
                                    <input type="text"
                                        wire:model='lockers_excep.{{ $i }}.observaciones'
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                </td>
                                <td class="px-6 py-4 ">
                                    <button type="button" wire:click="eliminar({{ $i }})"
                                        class="text-red-700 border border-red-700 hover:bg-red-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm py-1.5 px-3 text-center inline-flex items-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:focus:ring-red-800 dark:hover:bg-red-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                            fill="currentColor" class="w-5 h-5">
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
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para la imagen en grande -->
    <template x-teleport="body">
        <div x-show="open_image" x-cloak @keydown.escape.window="open_image = false"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4">

            <!-- Overlay para cerrar al hacer clic afuera -->
            <div class="fixed inset-0" @click="open_image = false"></div>

            <!-- Contenido del Modal -->
            <div class="relative bg-white p-4 rounded-xl max-w-3xl w-full z-10 shadow-2xl">
                <div class="flex justify-between items-center mb-3 border-b pb-2">
                    <h2 class="text-lg font-bold text-gray-800" x-text="selectedName"></h2>
                    <button @click="open_image = false"
                        class="text-gray-500 hover:text-gray-800 text-2xl font-bold">&times;</button>
                </div>

                <div class="flex justify-center bg-gray-100 rounded-lg p-2">
                    <img :src="selectedImage" :alt="selectedName"
                        class="h-[70vh] w-auto rounded-lg object-contain">
                </div>
            </div>
        </div>
    </template>

    <!--Botones de navegacion (cancelar, guardar)-->
    <div class="my-3">
        <a href="{{ route('lockers.consultarMovimientos') }}"
            class="inline-flex items-center focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">
            <svg class="w-6 h-6 dark:text-gray-800 text-white me-2" aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                viewBox="0 0 24 24">
                <path fill-rule="evenodd"
                    d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm7.707-3.707a1 1 0 0 0-1.414 1.414L10.586 12l-2.293 2.293a1 1 0 1 0 1.414 1.414L12 13.414l2.293 2.293a1 1 0 0 0 1.414-1.414L13.414 12l2.293-2.293a1 1 0 0 0-1.414-1.414L12 10.586 9.707 8.293Z"
                    clip-rule="evenodd" />
            </svg>
            Cancelar
        </a>
        <button type="button"
            x-on:click='locker_extraordinario ? $wire.guardarLockersExcepcionales : $wire.guardarLockers'
            wire:loading.attr="disabled" wire:target='saveLockers'
            class="inline-flex items-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
            <svg class="w-6 h-6 dark:text-gray-800 text-white me-2" aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                viewBox="0 0 24 24">
                <path fill-rule="evenodd"
                    d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z"
                    clip-rule="evenodd" />
            </svg>
            Confirmar movimiento
        </button>
    </div>
    {{-- ALERT --}}
    <x-action-message on='action-message-locker'>
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
    <!--INDICADOR DE CARGA, DE VENTA-->
    <div wire:loading wire:target='guardarLockers'>
        <x-loading-screen name='loading'>
            <x-slot name='body'>
                <div class="flex">
                    <div class="me-4">
                        @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
                    </div>
                    <p>Procesando lockers</p>
                </div>
            </x-slot>
        </x-loading-screen>
    </div>
</div>
