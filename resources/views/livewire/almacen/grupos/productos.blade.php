<div>
    <div class="flex gap-2">
        {{-- COLUMNA 1 GRUPOS --}}
        <div class="w-full">
            {{-- BOTON PARA AGREGAR UN NUEVO GRUPO --}}
            <div class="flex gap-2">
                <button type="button" wire:click='activarFormulario' @disabled($isFormActive)
                    class="px-3 py-2 text-sm font-medium text-center inline-flex items-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800
                    {{ $isFormActive ? 'opacity-50 cursor-not-allowed' : '' }}">
                    <svg class="w-4 h-4 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 12h14m-7 7V5" />
                    </svg>
                </button>
                <p class="text-lg font-bold text-gray-900 dark:text-white">GRUPOS</p>
            </div>
            <div class="flex gap-2 my-2">
                {{-- LISTA DE GRUPOS --}}
                <div class="w-full" style="max-height: 300px; overflow-y: auto;">
                    @foreach ($listaGrupos as $grupos)
                        <div
                            class="w-full inline-flex text-gray-900 bg-white border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white
                            {{ $isFormActive && $selectedProductoId != $grupos->id ? 'opacity-50' : '' }}
                            {{ $isFormActive && $selectedProductoId == $grupos->id ? 'ring-2 ring-blue-500' : '' }}">
                            <button type="button" wire:click='selectGrupo({{ $grupos->id }})'
                                @disabled($isFormActive && $selectedProductoId == $grupos->id) {{-- Deshabilitar si ya está seleccionado y el form activo --}}
                                @if ($isFormActive && $selectedProductoId != $grupos->id) disabled @endif {{-- Deshabilitar otros items si el form está activo --}}
                                class="relative inline-flex items-center w-full px-4 py-2 text-sm font-medium border-b border-gray-200 rounded-t-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:border-gray-600 dark:hover:bg-gray-600 dark:hover:text-white dark:focus:ring-gray-500 dark:focus:text-white
                                       {{ $selectedProductoId == $grupos->id ? 'bg-blue-100 dark:bg-blue-800' : '' }}
                                       {{ ($isFormActive && $selectedProductoId != $grupos->id) || ($isFormActive && $selectedProductoId == $grupos->id && !$errors->any()) ? 'cursor-not-allowed' : '' }}">
                                {{ $grupos->id }} {{ $grupos->descripcion }}
                                <button type="button" wire:click='deleteGrupo({{ $grupos->id }})'
                                    class="text-red-700 border border-red-700 hover:bg-red-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center  dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:focus:ring-red-800 dark:hover:bg-red-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="w-4 h-4">
                                        <path fill-rule="evenodd"
                                            d="M8.586 2.586A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4a2 2 0 0 1 .586-1.414ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </button>
                        </div>
                    @endforeach
                </div>
                {{-- FORMULARIO DE REGISTRO --}}
                <div class="w-full {{ !$isFormActive ? 'opacity-50 pointer-events-none' : '' }}">
                    {{-- DESCRIPCION --}}
                    <div class="mb-6">
                        <label for="descripcion-grupo"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Descripción
                            Grupo</label>
                        <input type="text" id="descripcion-grupo" wire:model='descripcionProd'
                            @disabled(!$isFormActive)
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        @error('descripcionProd')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>

                    {{-- CLASIFICACION --}}
                    <form class="max-w-sm mx-auto">
                        <label for="clasificacion"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Clasificación</label>
                        <select id="clasificacion" wire:model='clasificacionProd' @disabled(!$isFormActive)
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option selected value="{{ null }}">SELECCIONAR</option>
                            @foreach ($clasificaciones as $clasificacion)
                                <option value="{{ $clasificacion }}">{{ $clasificacion }}</option>
                            @endforeach
                        </select>
                        @error('clasificacionProd')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </form>
                </div>
            </div>
        </div>

        {{-- COLUMNA 2 SUBGRUPOS --}}
        <div class="w-full {{ !$isFormActive ? 'opacity-50 pointer-events-none' : '' }}">
            <div class="flex gap-2">
                <p class="text-lg font-bold text-gray-900 dark:text-white">SUBGRUPOS</p>
            </div>
            <div class="flex gap-2 my-2">
                {{-- LISTA DE SUBGRUPOS --}}
                <div class="w-full" style="max-height: 300px; overflow-y: auto;">
                    @foreach ($subgrupos as $index => $subgrupo)
                        <div
                            class="w-full inline-flex text-gray-900 bg-white border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <button type="button"
                                class="relative inline-flex items-center w-full px-4 py-2 text-sm font-medium border-b border-gray-200 rounded-t-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:border-gray-600 dark:hover:bg-gray-600 dark:hover:text-white dark:focus:ring-gray-500 dark:focus:text-white">
                                {{ $subgrupo }}
                                <button type="button" wire:click="eliminarSubgrupo({{ $index }})"
                                    class="text-red-700 border border-red-700 hover:bg-red-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center  dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:focus:ring-red-800 dark:hover:bg-red-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="w-4 h-4">
                                        <path fill-rule="evenodd"
                                            d="M8.586 2.586A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4a2 2 0 0 1 .586-1.414ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </button>
                        </div>
                    @endforeach
                </div>
                {{-- AGREGAR SUBGRUPO --}}
                <div class="w-full">
                    <div class="mb-6">
                        <label for="default-input"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Descripción
                            Subgrupo</label>
                        <input type="text" id="default-input" wire:model.live='descripcion_subgrupo'
                            wire:keydown.enter='crearSubgrupo' @disabled(!$isFormActive)
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Ingrese subgrupo y presione Enter">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BOTON DE CANCELAR --}}
    <button wire:click='cancelarEdit' @disabled(!$isFormActive)
        class="my-2 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800
        {{ !$isFormActive ? 'opacity-50 cursor-not-allowed' : '' }}">
        <svg class="w-5 h-5 me-2 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
            height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M6 18 17.94 6M18 18 6.06 6" />
        </svg>
        Cancelar
    </button>
    {{-- BOTON DE GUARDAR --}}
    <button wire:click='register' @disabled(!$isFormActive)
        class="my-2 text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800
        {{ !$isFormActive ? 'opacity-50 cursor-not-allowed' : '' }}">
        <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
            fill="currentColor" viewBox="0 0 24 24">
            <path fill-rule="evenodd"
                d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm10 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM8 7V5h8v2a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1Z"
                clip-rule="evenodd" />
        </svg>
        {{ $selectedProductoId ? 'Actualizar Grupo' : 'Guardar Grupo' }}
    </button>

    <!--Modal de eliminacion -->
    <x-modal title="Eliminar Grupo" name="modalEliminar">
        <x-slot name='body'>
            <div class="text-center">
                <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <h3 class="mb-5 text-xl font-normal text-gray-500 dark:text-gray-400">¿Desea eliminar a: <span
                        class="font-bold">{{ $eliminarGrupoDescripcion }}</span> ?
                </h3>
                <p class="text-gray-500 dark:text-gray-400">Esta acción eliminará los subgrupos asociados a este
                    grupo.
                </p>

            </div>
        </x-slot>
        <x-slot name='footer'>
            <button type="button" wire:click='eliminarGrupo()'
                class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                Eliminar
            </button>
        </x-slot>
    </x-modal>

    <!--Alerts-->
    <x-action-message on='open-action-message'>
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
</div>
