<div x-data="{
    selected: [],
    activeTab: 0,
    modificadores_filtrados: [],
    alert: { open: false, message: '' },
    tabClasses(indexTab) {
        const isActive = this.activeTab == indexTab;
        let classes = 'inline-block w-full p-3 border-r border-gray-200 dark:border-gray-700 focus:ring-4 focus:ring-blue-300 focus:outline-none ';

        if (isActive) {
            classes += 'text-gray-900 bg-gray-100 dark:bg-gray-700 dark:text-white';
        } else {
            classes += 'bg-white hover:text-gray-700 hover:bg-gray-50 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700';
        }
        return classes;
    },
    filtrar_modificadores(id_grupo) {
        this.modificadores_filtrados = $wire.modificadores.filter(modif => modif.id_grupo == id_grupo);
    },
    cancelar() {
        $wire.limpiarCompuesto();
        this.selected = [];
        this.activeTab = 0;
        this.modificadores_filtrados = [];
        this.cerrarAlert();
    },
    guardar() {
        //Forzar captura de los grupos
        for (i = 0; i < $wire.gruposModif.length; i++) {
            let grupo_i = $wire.gruposModif[i]; //grupo de modificador de la iteracion
            let modif_i = this.selected.filter(modif => modif.id_grupo == grupo_i.id_grupo); //Modificadores seleccionados del grupo en la iteracion
            //Cantidad total de modificadores
            let total = modif_i.reduce((acu, currentVal) => acu + currentVal.cantidad, 0)

            if (grupo_i.forzar_captura == 1 && (total < $wire.cantidadProducto)) {
                this.mostrarAlert('Falta capturar: ' + grupo_i.descripcion);
                return false;
            }
        }
        $wire.guardarCompuesto(this.selected);
        this.cancelar();
    },
    agregar(modificador) {
        //Definir variables auxiliares
        let grupo_actual = $wire.gruposModif.filter(grupo => grupo.id_grupo == modificador.id_grupo)[0];
        let modif_seleccionados = this.selected.filter(modif => modif.id_grupo == modificador.id_grupo);
        let clon_modificador = Object.assign({ cantidad: 1 }, modificador);
        //Cantidad de modificades seleccionados que ya estan incluidos en el precio 
        let incluidos = modif_seleccionados.reduce((acu, currentVal) =>
            currentVal.precio == 0 ? acu + currentVal.cantidad : acu + 0, 0)
        //Cantidad total de modificadores
        let total = modif_seleccionados.reduce((acu, currentVal) => acu + currentVal.cantidad, 0)
        //Si la cantidad de modificadores seleccionados es menor a la cantidad de modificadores incluidos en el prcio
        if (incluidos < grupo_actual.modif_incluidos * $wire.cantidadProducto)
            clon_modificador.precio = 0; //cambiar el precio del modificador a 0.
        //Verificar si es el maximo de modificadores para el grupo
        if (total >= grupo_actual.modif_maximos * $wire.cantidadProducto) {
            this.mostrarAlert('Has alcanzado el maximo para el grupo');
        } else {
            //Agregar el caracter iniciar del modificador
            clon_modificador.descripcion = '> ' + clon_modificador.descripcion;
            //filtrar aquel modificador, cuya 'clave_modificador' y 'precio' sea el mismo 
            for (i = 0; i < this.selected.length; i++) {
                let modif_i = this.selected[i]; //el modificador de la iteracion actual
                if (modif_i.clave_modificador == clon_modificador.clave_modificador && modif_i.precio == clon_modificador.precio) {
                    this.selected[i].cantidad = this.selected[i].cantidad + 1;
                    return;
                }
            }
            this.selected.push(clon_modificador);
        }
    },
    mostrarAlert(message) {
        this.alert.open = true;
        this.alert.message = message;
    },
    cerrarAlert() {
        this.alert.open = false;
        this.alert.message = '';
    }
}"
    x-on:actualizar-modificadores.window="
        let grupo = $wire.gruposModif[0];
        activeTab = grupo.id_grupo; filtrar_modificadores(grupo.id_grupo);
    ">
    <div wire:loading.class='animate-pulse pointer-events-none' wire:target='guardarCompuesto'>
        {{-- TABS --}}
        <ul
            class="hidden text-sm font-medium text-center text-gray-500 rounded-lg shadow-sm sm:flex dark:divide-gray-700 dark:text-gray-400">
            <template x-for="(grupo, index) in $wire.gruposModif" :key="index">
                <li class="w-full focus-within:z-10">
                    <button x-on:click="activeTab =  grupo.id_grupo; filtrar_modificadores(grupo.id_grupo)"
                        :class="tabClasses(grupo.id_grupo)" x-text="grupo.descripcion">
                    </button>
                </li>
            </template>
        </ul>
        {{-- Table & List --}}
        <div class="flex gap-4 mt-2">
            {{-- Table --}}
            <div class="h-96 overflow-y-auto shadow-md sm:rounded-lg">
                <table class=" text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-3 py-2 w-72">
                                DESCRIPCION
                            </th>
                            <th scope="col" class="px-3 py-2 w-8">
                                CANT.
                            </th>
                            <th scope="col" class="px-3 py-2 w-16">
                                COSTO
                            </th>
                            <th scope="col" class="px-3 py-2 w-16">
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(modif, index) in selected" :key="index">
                            <tr
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <th scope="row" x-text="modif.descripcion"
                                    class="w-72 max-w-72 overflow-hidden text-nowrap text-ellipsis px-3 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                </th>
                                <td class="w-16 px-3 py-2" x-text="modif.cantidad">
                                </td>
                                <td class="w-16 px-3 py-2" x-text="modif.precio">
                                </td>
                                <td class="w-16 px-3 py-2">
                                    <button type="button" x-on:click="selected.splice(index,1)"
                                        class="text-gray-700 border border-gray-700 hover:bg-gray-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm p-1 text-center inline-flex items-center dark:border-gray-500 dark:text-gray-500 dark:hover:text-white dark:focus:ring-gray-800 dark:hover:bg-gray-500">
                                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd"
                                                d="M8.586 2.586A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4a2 2 0 0 1 .586-1.414ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </td>
                            <tr>
                        </template>
                    </tbody>
                </table>
            </div>
            {{-- List butons --}}
            <div style="max-height: 350px; overflow-y: auto;"
                class="w-64 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <template x-for="(modif, index) in modificadores_filtrados" :key="index">
                    <button type="button" x-text="modif.descripcion" x-on:click="agregar(modif)"
                        class="w-full px-4 py-2 font-medium text-left rtl:text-right border-b border-gray-200 cursor-pointer hover:bg-gray-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:border-gray-600 dark:hover:bg-gray-600 dark:hover:text-white dark:focus:ring-gray-500 dark:focus:text-white">
                    </button>
                </template>
            </div>
        </div>
        <!-- Modal footer -->
        <div class="mt-3 flex gap-3 items-center  border-t border-gray-200 rounded-b dark:border-gray-600">
            <button type="button" x-on:click="guardar()"
                class="px-5 py-2.5 text-sm font-medium text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                    <path fill-rule="evenodd"
                        d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z"
                        clip-rule="evenodd" />
                </svg>
                Confirmar
            </button>
            <button type="button" x-on:click="cancelar()"
                class="px-5 py-2.5 text-sm font-medium text-white inline-flex items-center bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 rounded-lg text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                    <path fill-rule="evenodd"
                        d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z"
                        clip-rule="evenodd" />
                </svg>
                Cancelar
            </button>
            <div wire:loading wire:target='guardarCompuesto'>
                @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
            </div>
            {{-- Alert --}}
            <div x-show="alert.open" x-transition>
                <div class="flex items-center p-3 text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
                    role="alert">
                    <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                    </svg>
                    <span class="sr-only">Info</span>
                    <div class="ms-3 text-sm font-medium" x-text="alert.message">
                    </div>
                    <button type="button" x-on:click="cerrarAlert()"
                        class="ms-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-gray-700"
                        aria-label="Close">
                        <span class="sr-only">Close</span>
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
