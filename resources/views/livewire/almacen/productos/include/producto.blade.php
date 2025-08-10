<div x-data="{
    activeTab: 'general',
    tabClasses(tabName) {
        const isActive = this.activeTab === tabName;
        let classes = 'inline-block w-full p-4 border-r border-gray-200 dark:border-gray-700 focus:ring-4 focus:ring-blue-300 focus:outline-none ';

        if (isActive) {
            classes += 'text-gray-900 bg-gray-100 dark:bg-gray-700 dark:text-white';
        } else {
            classes += 'bg-white hover:text-gray-700 hover:bg-gray-50 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700';
        }
        return classes;
    }
}">
    {{-- TABS --}}
    <ul
        class="hidden text-sm font-medium text-center text-gray-500 rounded-lg shadow-sm sm:flex dark:divide-gray-700 dark:text-gray-400">
        <li class="w-full focus-within:z-10">
            <button @click="activeTab = 'general'" :class="tabClasses('general')"
                class="inline-block w-full p-3 border-r border-gray-200 dark:border-gray-700 rounded-s-lg focus:ring-4 focus:ring-blue-300 focus:outline-none hover:text-gray-700 hover:bg-gray-50 dark:hover:text-white dark:hover:bg-gray-700">
                General
            </button>
        </li>
        <li class="w-full focus-within:z-10">
            <button @click="activeTab = 'receta'" :class="tabClasses('receta')"
                class="inline-block w-full p-3 border-r border-gray-200 dark:border-gray-700 focus:ring-4 focus:ring-blue-300 focus:outline-none hover:text-gray-700 hover:bg-gray-50 dark:hover:text-white dark:hover:bg-gray-700">
                Receta
            </button>
        </li>
        <li class="w-full focus-within:z-10">
            <button @click="activeTab = 'compuesto'" :class="tabClasses('compuesto')"
                class="inline-block w-full p-3 border-r border-gray-200 dark:border-gray-700 focus:ring-4 focus:ring-blue-300 focus:outline-none hover:text-gray-700 hover:bg-gray-50 dark:hover:text-white dark:hover:bg-gray-700">
                Compuesto
            </button>
        </li>
    </ul>
    {{-- TAB CONTENT --}}
    <div class="p-4">
        <div x-show="activeTab === 'general'" x-cloak>
            {{-- Grid --}}
            <div class="grid grid-cols-2 gap-4">
                {{-- Columna 1 --}}
                <div>
                    {{-- Descripcion --}}
                    <div>
                        <label for="descripcion"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Descripcion
                            producto</label>
                        <input type="text" id="descripcion" wire:model='form.descripcion'
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        @error('form.descripcion')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    {{-- precio --}}
                    <div>
                        <label for="precio"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Precio venta</label>
                        <input type="number" id="precio" wire:model='form.precio' wire:change='changedPrecio'
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        @error('form.precio')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    {{-- iva --}}
                    <div>
                        <label for="iva"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Iva</label>
                        <input type="number" id="iva" wire:model='form.iva' wire:change='changedIva'
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        @error('form.iva')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    {{-- costo con impuesto --}}
                    <div>
                        <label for="c_c_impuesto"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Precio con
                            impuesto</label>
                        <input type="number" id="c_c_impuesto" wire:model='form.costo_con_impuesto'
                            wire:change='changedPrecioIva'
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        @error('form.costo_con_impuesto')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                </div>
                {{-- Columna 2 --}}
                <div>
                    {{-- Grupo --}}
                    <div>
                        <label for="grupo"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Grupo</label>
                        <select id="grupo" wire:model.live='form.id_grupo'
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="{{ null }}">Seleccione</option>
                            @foreach ($this->grupos as $index_grup => $item)
                                <option wire:key='{{ $index_grup }}' value="{{ $item->id }}">
                                    {{ $item->descripcion }}</option>
                            @endforeach
                        </select>
                        @error('form.id_grupo')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    {{-- SubGrupo --}}
                    <div wire:loading.class='opacity-50 w-full' wire:target='form.id_grupo'>
                        <label for="subgrupo"
                            class=" block mb-2 text-sm font-medium text-gray-900 dark:text-white">Subgrupo</label>
                        <select id="subgrupo" wire:model='form.id_subgrupo'
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="{{ null }}">Seleccione</option>
                            @foreach ($this->subgrupos as $index_sub => $item)
                                <option wire:key='{{ $index_sub }}' value="{{ $item->id }}">
                                    {{ $item->descripcion }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Select Estado --}}
                    <div class="flex gap-2 w-full">
                        <div class="w-full">
                            <label for="Estado"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estado</label>
                            <select id="Estado" wire:model='form.estado'
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option selected value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                            @error('form.estado')
                                <x-input-error messages="{{ $message }}" />
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div x-show="activeTab === 'receta'" x-cloak>
            {{-- Componente de busqueda de insumos --}}
            <livewire:search-bar tittle="Buscar insumo" table="insumos" :columns="['clave', 'descripcion']" primary="clave"
                event="selected-receta" :conditions="[['inventariable', '=', 1]]" />
            {{-- Tabla resultados --}}
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-3 py-2">
                                #
                            </th>
                            <th scope="col" class="px-3 py-2">
                                Insumo
                            </th>
                            <th scope="col" class="px-3 py-2">
                                Cantidad
                            </th>
                            <th scope="col" class="px-3 py-2">
                                Cantidad c/merma
                            </th>
                            <th scope="col" class="px-3 py-2">
                                Unidad
                            </th>
                            <th scope="col" class="px-3 py-2">
                                Total
                            </th>
                            <th scope="col" class="px-3 py-2">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->form->receta_table as $index => $insumo)
                            @if (!array_key_exists('deleted', $insumo))
                                <tr wire:key='{{ $index }}'
                                    class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                    <th scope="row"
                                        class="w-24 px-3 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $insumo['clave'] }}
                                    </th>
                                    <td class="w-6/12 px-3 py-2">
                                        {{ $insumo['descripcion'] }}
                                    </td>
                                    <td class="px-3 py-2 w-32">
                                        <input type="number"
                                            wire:model='form.receta_table.{{ $index }}.cantidad'
                                            wire:change='actualizarTotal' step="0.001" placeholder="0.001"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                                    </td>
                                    <td class="px-3 py-2 w-32">
                                        <input type="number"
                                            wire:model='form.receta_table.{{ $index }}.cantidad_con_merma'
                                            step="0.001" placeholder="0.001"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                                    </td>
                                    <td class="px-3 py-2 w-32">
                                        {{ $insumo['unidad']['descripcion'] }}
                                    </td>
                                    <td class="px-3 py-2">
                                        $ {{ number_format($insumo['total'], 2) }}
                                    </td>
                                    <td class="px-3 py-2">
                                        <button type="button" wire:click="eliminarInsumo({{ $index }})"
                                            class="text-red-700 border border-red-700 hover:bg-red-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center  dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:focus:ring-red-800 dark:hover:bg-red-500">
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
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div x-show="activeTab === 'compuesto'" x-cloak>
            {{-- grid --}}
            <div class="grid grid-cols-2 gap-3">
                {{-- Tabla 1 --}}
                <div>
                    {{-- Componente de busqueda de grupos --}}
                    <livewire:search-bar tittle="Buscar grupo modificador" table="grupos_modificadores"
                        :columns="['id', 'descripcion']" primary="id" event="selected-grupo" />
                    {{-- Tabla grupo modificador --}}
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-3 py-2">
                                        #
                                    </th>
                                    <th scope="col" class="px-3 py-2">
                                        Grupo modificador
                                    </th>
                                    <th scope="col" class="px-3 py-2">
                                        M.Incluidos
                                    </th>
                                    <th scope="col" class="px-3 py-2">
                                        M.Maximos
                                    </th>
                                    <th scope="col" class="px-3 py-2">
                                        Forzar
                                    </th>
                                    <th scope="col" class="px-3 py-2">
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($this->form->grupos_modif as $i_grup_mod => $grupo_mod)
                                    @if (!array_key_exists('deleted', $grupo_mod))
                                        <tr wire:key='{{ $i_grup_mod }}'
                                            class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                            <th scope="row"
                                                class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ $grupo_mod['id_grupo'] }}
                                            </th>
                                            <td class="px-3 py-2 w-full">
                                                {{ $grupo_mod['descripcion'] }}
                                            </td>
                                            <td class="px-3 py-2 w-32">
                                                <input type="number" step="1"
                                                    wire:model='form.grupos_modif.{{ $i_grup_mod }}.incluidos'
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                                                @error('form.grupos_modif.' . $i_grup_mod . '.incluidos')
                                                    <x-input-error messages="{{ $message }}" />
                                                @enderror
                                            </td>
                                            <td class="px-3 py-2 w-32">
                                                <input type="number" step="1"
                                                    wire:model='form.grupos_modif.{{ $i_grup_mod }}.maximos'
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                                                @error('form.grupos_modif.' . $i_grup_mod . '.maximos')
                                                    <x-input-error messages="{{ $message }}" />
                                                @enderror
                                            </td>
                                            <td class="px-3 py-2 text-center">
                                                <input type="checkbox"
                                                    wire:model='form.grupos_modif.{{ $i_grup_mod }}.forzar'
                                                    class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                            </td>
                                            <td class="px-3 py-2">
                                                <button type="button"
                                                    wire:click="eliminarGrupo({{ $i_grup_mod }})"
                                                    class="text-red-700 border border-red-700 hover:bg-red-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center  dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:focus:ring-red-800 dark:hover:bg-red-500">
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
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- Link, grupos de modif --}}
                    <a href="{{ route('almacen.grupos') }}"
                        class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Grupos
                        modificadores</a>
                </div>
                {{-- Tabla 2 --}}
                <div>
                    {{-- Componente de busqueda de productos --}}
                    <livewire:search-bar tittle="Buscar modificador (producto)" table="productos" :columns="['clave', 'descripcion']"
                        primary="clave" event="selected-producto" :conditions="[['estado', '=', true]]" />
                    {{-- Tabla de productos (modificadores posibles) --}}
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-3 py-2">
                                        #
                                    </th>
                                    <th scope="col" class="px-3 py-2">
                                        Modificador (producto)
                                    </th>
                                    <th scope="col" class="px-3 py-2">
                                        precio
                                    </th>
                                    <th scope="col" class="px-3 py-2">
                                        Grupo.
                                    </th>
                                    <th scope="col" class="px-3 py-2">
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($this->form->modif as $i_modif => $item)
                                    @if (!array_key_exists('deleted', $item))
                                        <tr wire:key='{{ $i_modif }}'
                                            class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                            <th scope="row"
                                                class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ $item['clave'] }}
                                            </th>
                                            <td class="px-3 py-2 w-full">
                                                {{ $item['descripcion'] }}
                                            </td>
                                            <td class="px-3 py-2 ">
                                                <div class="flex items-center gap-2 w-32">
                                                    $ <input type="number" step="0.001"
                                                        wire:model='form.modif.{{ $i_modif }}.precio'
                                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                                                </div>
                                                @error('form.modif.' . $i_modif . '.precio')
                                                    <x-input-error messages="{{ $message }}" />
                                                @enderror
                                            </td>
                                            <td class="px-3 py-2">
                                                <select wire:model='form.modif.{{ $i_modif }}.id_grup_modif'
                                                    class="w-fit bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                    <option value="{{ null }}">Seleccione</option>
                                                    @foreach ($this->form->grupos_modif as $i_grupo => $grupo)
                                                        @if (!array_key_exists('deleted', $grupo))
                                                            <option wire:key='{{ $i_grupo }}'
                                                                value="{{ $grupo['id_grupo'] }}">
                                                                {{ $grupo['descripcion'] }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                @error('form.modif.' . $i_modif . '.id_grup_modif')
                                                    <x-input-error messages="{{ $message }}" />
                                                @enderror
                                            </td>
                                            <td class="px-3 py-2">
                                                <button type="button"
                                                    wire:click="eliminarProductoModif({{ $i_modif }})"
                                                    class="text-red-700 border border-red-700 hover:bg-red-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center  dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:focus:ring-red-800 dark:hover:bg-red-500">
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
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Botones de accion --}}
    <div class="px-4 py-2">
        {{-- Boton de regresar --}}
        <a type="button" href="{{ route('almacen.productos') }}"
            class="my-2 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
            <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 12h14M5 12l4-4m-4 4 4 4" />
            </svg>Regresar
        </a>
        {{-- Boton de guardar cambios --}}
        <a type="button" wire:click='guardar'
            class="my-2 text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
            <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                height="24" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd"
                    d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm10 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM8 7V5h8v2a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1Z"
                    clip-rule="evenodd" />
            </svg>
            Guardar producto
        </a>
    </div>
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

    <!--INDICADOR DE CARGA-->
    <div wire:loading wire:target='guardar'>
        <x-loading-screen name='loading'>
            <x-slot name='body'>
                <div class="flex">
                    <div class="me-4">
                        @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
                    </div>
                    <p>Guardando producto nuevo</p>
                </div>
            </x-slot>
        </x-loading-screen>
    </div>
</div>
