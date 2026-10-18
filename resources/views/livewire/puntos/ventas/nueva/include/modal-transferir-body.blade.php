<div x-data="{
    paso: 1,
    ventas_abiertas: [],
    ventas_mostradas: [],
    busqueda: '',
    producto: null,
    modificadores_capturados: [],
    grupos_reglas: [],
    modificadores_catalogo: [],
    ventas_participantes: [],
    alert: { open: false, message: '' },

    mostrarAlert(msg) {
        this.alert.open = true;
        this.alert.message = msg;
    },
    cerrarAlert() {
        this.alert.open = false;
        this.alert.message = '';
    },
    buscar(e) {
        let input = (e.target.value || '').toLowerCase().trim();
        if (!input) {
            this.ventas_mostradas = this.ventas_abiertas;
            return;
        }
        this.ventas_mostradas = this.ventas_abiertas.filter(venta =>
            (venta.id_socio && venta.id_socio.toString().toLowerCase().includes(input)) ||
            (venta.nombre && venta.nombre.toLowerCase().includes(input)) ||
            (venta.folio && venta.folio.toString().includes(input))
        );
    },
    totalAsignadoDestinos() {
        return this.ventas_abiertas.reduce((sum, v) => sum + (parseInt(v.cantidad_destino) || 0), 0);
    },
    remanenteOrigen() {
        return this.producto ? (this.producto.cantidad - this.totalAsignadoDestinos()) : 0;
    },
    obtenerNombreGrupo(id_grupo) {
        let g = this.grupos_reglas.find(gr => gr.id_grupo == id_grupo);
        return g ? g.descripcion : 'General';
    },
    continuarPaso1() {
        this.cerrarAlert();
        let totalAsignado = this.totalAsignadoDestinos();
        if (totalAsignado <= 0) {
            this.mostrarAlert('Debe asignar al menos una cantidad mayor a 0 en una venta destino.');
            return;
        }
        if (totalAsignado > this.producto.cantidad) {
            this.mostrarAlert('La cantidad total transferida (' + totalAsignado + ') excede las unidades disponibles (' + this.producto.cantidad + ').');
            return;
        }

        let destinosConCantidad = this.ventas_abiertas.filter(v => (parseInt(v.cantidad_destino) || 0) > 0);

        // Caso Fast-Path: 100% de la cantidad a una sola venta destino
        if (destinosConCantidad.length === 1 && totalAsignado === this.producto.cantidad) {
            $wire.confirmarTransferencia({
                es_fast_path: true,
                folio_destino: destinosConCantidad[0].folio
            });
            return;
        }

        // Caso sin modificadores asociados
        if (!this.modificadores_capturados || this.modificadores_capturados.length === 0) {
            $wire.confirmarTransferencia({
                es_fast_path: false,
                ventas_destino: destinosConCantidad.map(d => ({
                    folio: d.folio,
                    cantidad_destino: parseInt(d.cantidad_destino),
                    modificadores: []
                })),
                remanente_origen: {
                    cantidad: this.remanenteOrigen(),
                    modificadores: []
                }
            });
            return;
        }

        // Caso con modificadores: preparar y pasar al Paso 2
        this.prepararPaso2(destinosConCantidad);
        this.paso = 2;
    },
    prepararPaso2(destinos) {
        this.ventas_participantes = [];
        destinos.forEach(d => {
            let asignaciones = {};
            this.modificadores_capturados.forEach(m => {
                asignaciones[m.clave_producto] = 0;
            });
            this.ventas_participantes.push({
                tipo: 'destino',
                folio: d.folio,
                titulo: '#' + d.folio + ' ' + (d.nombre || 'Sin nombre'),
                cantidad: parseInt(d.cantidad_destino),
                asignaciones: asignaciones
            });
        });

        let rem = this.remanenteOrigen();
        if (rem > 0) {
            let asignaciones = {};
            this.modificadores_capturados.forEach(m => {
                asignaciones[m.clave_producto] = 0;
            });
            this.ventas_participantes.push({
                tipo: 'origen',
                folio: 'origen',
                titulo: 'Origen (Venta Actual)',
                cantidad: rem,
                asignaciones: asignaciones
            });
        }
    },
    sumaAsignadaModificador(clave_producto) {
        return this.ventas_participantes.reduce((sum, v) => sum + (parseInt(v.asignaciones[clave_producto]) || 0), 0);
    },
    sinAsignarModificador(mod) {
        return mod.cantidad - this.sumaAsignadaModificador(mod.clave_producto);
    },
    totalAsignadoGrupo(venta, id_grupo) {
        let modsDelGrupo = this.modificadores_capturados.filter(m => m.id_grupo == id_grupo);
        return modsDelGrupo.reduce((sum, m) => sum + (parseInt(venta.asignaciones[m.clave_producto]) || 0), 0);
    },
    puedeSumarModificador(vp, mod) {
        if (this.sinAsignarModificador(mod) <= 0) return false;
        let grupo = this.grupos_reglas.find(g => g.id_grupo == mod.id_grupo);
        if (grupo && grupo.modif_maximos) {
            let totalGrupo = this.totalAsignadoGrupo(vp, mod.id_grupo);
            if (totalGrupo >= grupo.modif_maximos * vp.cantidad) {
                return false;
            }
        }
        return true;
    },
    sumarModificador(vp, mod) {
        this.cerrarAlert();
        if (this.puedeSumarModificador(vp, mod)) {
            vp.asignaciones[mod.clave_producto]++;
        } else if (this.sinAsignarModificador(mod) > 0) {
            let grupo = this.grupos_reglas.find(g => g.id_grupo == mod.id_grupo);
            let desc = grupo ? grupo.descripcion : 'este grupo';
            let max = grupo ? (grupo.modif_maximos * vp.cantidad) : 0;
            this.mostrarAlert('En ' + vp.titulo + ' ya alcanzó el máximo permitido de ' + max + ' para ' + desc + '.');
        }
    },
    confirmarPaso2() {
        this.cerrarAlert();
        // 1. Validar que la totalidad de cada modificador este asignada exactamente
        for (let i = 0; i < this.modificadores_capturados.length; i++) {
            let m = this.modificadores_capturados[i];
            let sinAsignar = this.sinAsignarModificador(m);
            if (sinAsignar !== 0) {
                this.mostrarAlert('Falta o sobra repartir el modificador: ' + m.nombre + '. (Diferencia: ' + sinAsignar + ')');
                return;
            }
        }

        // 2. Validar reglas de grupos por cada venta participante
        for (let vIdx = 0; vIdx < this.ventas_participantes.length; vIdx++) {
            let venta = this.ventas_participantes[vIdx];
            for (let gIdx = 0; gIdx < this.grupos_reglas.length; gIdx++) {
                let grupo = this.grupos_reglas[gIdx];
                let totalAsignado = this.totalAsignadoGrupo(venta, grupo.id_grupo);
                let maximo = grupo.modif_maximos * venta.cantidad;
                let minimo = grupo.forzar_captura ? venta.cantidad : 0;

                if (totalAsignado > maximo) {
                    this.mostrarAlert('En ' + venta.titulo + ', el grupo ' + grupo.descripcion + ' excede el máximo permitido (' + totalAsignado + ' de ' + maximo + ').');
                    return;
                }
                if (totalAsignado < minimo) {
                    this.mostrarAlert('En ' + venta.titulo + ', el grupo ' + grupo.descripcion + ' requiere al menos ' + minimo + ' modificador(es) (actualmente tiene ' + totalAsignado + ').');
                    return;
                }
            }
        }

        // 3. Preparar payload para Livewire
        let ventasDestinoPayload = [];
        let remanenteOrigenPayload = { cantidad: 0, modificadores: [] };

        this.ventas_participantes.forEach(vp => {
            let mods = [];
            this.modificadores_capturados.forEach(m => {
                let cant = parseInt(vp.asignaciones[m.clave_producto]) || 0;
                if (cant > 0) {
                    mods.push({
                        clave_producto: m.clave_producto,
                        nombre: m.nombre,
                        cantidad: cant,
                        id_grupo: m.id_grupo,
                        id_zona: m.id_zona,
                        print_default: m.print_default
                    });
                }
            });

            if (vp.tipo === 'destino') {
                ventasDestinoPayload.push({
                    folio: vp.folio,
                    cantidad_destino: vp.cantidad,
                    modificadores: mods
                });
            } else {
                remanenteOrigenPayload = {
                    cantidad: vp.cantidad,
                    modificadores: mods
                };
            }
        });

        $wire.confirmarTransferencia({
            es_fast_path: false,
            ventas_destino: ventasDestinoPayload,
            remanente_origen: remanenteOrigenPayload
        });
    }
}"
    x-on:inicializar-transferencia.window="
        paso = 1;
        alert.open = false;
        alert.message = '';
        producto = $event.detail.producto || $event.detail.product;
        modificadores_capturados = $event.detail.modificadores_capturados || [];
        grupos_reglas = $event.detail.grupos_reglas || [];
        modificadores_catalogo = $event.detail.modificadores_catalogo || [];
        ventas_abiertas = ($event.detail.ventas_abiertas || []).map(v => Object.assign({}, v, { cantidad_destino: 0 }));
        ventas_mostradas = ventas_abiertas;
    "
    class="space-y-2.5 w-full min-w-[700px] max-w-4xl text-gray-800 dark:text-gray-200">

    {{-- Alert de validacion --}}
    <div x-show="alert.open" x-transition
        class="flex items-center p-3 text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 text-sm"
        role="alert">
        <svg class="shrink-0 w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
            viewBox="0 0 20 20">
            <path
                d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
        </svg>
        <div class="font-medium grow" x-text="alert.message"></div>
        <button type="button" x-on:click="cerrarAlert()"
            class="ms-auto bg-red-50 text-red-500 rounded p-1 hover:bg-red-200 inline-flex items-center dark:bg-gray-800 dark:text-red-400">
            <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
            </svg>
        </button>
    </div>

    {{-- Indicador de Pasos --}}
    <div class="flex items-center justify-between border-b pb-2 dark:border-gray-700">
        <div class="flex items-center space-x-2 text-sm">
            <span
                :class="paso === 1 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300'"
                class="px-2.5 py-0.5 font-bold rounded-full text-xs">1</span>
            <span class="font-semibold text-gray-900 dark:text-white">Cantidades por Venta</span>
        </div>
        <template x-if="modificadores_capturados.length > 0">
            <div class="flex items-center space-x-2 text-sm">
                <span
                    :class="paso === 2 ? 'bg-blue-600 text-white' :
                        'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300'"
                    class="px-2.5 py-0.5 font-bold rounded-full text-xs">2</span>
                <span class="font-semibold text-gray-900 dark:text-white">Distribuir Modificadores</span>
            </div>
        </template>
    </div>

    {{-- Resumen del Producto (Legible y Compacto) --}}
    <template x-if="producto">
        <div
            class="flex flex-wrap items-center justify-between gap-2 px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-lg dark:bg-gray-800 dark:border-gray-700 text-sm">
            <div>
                <span class="text-gray-500 dark:text-gray-400">Producto:</span>
                <span class="font-bold text-gray-900 dark:text-white ms-1" x-text="producto.nombre"></span>
            </div>
            <div class="flex items-center gap-4">
                <div>
                    <span class="text-gray-500 dark:text-gray-400">Total:</span>
                    <span class="font-bold text-blue-600 dark:text-blue-400 ms-1"
                        x-text="producto.cantidad + ' pzas'"></span>
                </div>
                <div>
                    <span class="text-gray-500 dark:text-gray-400">A transferir:</span>
                    <span class="font-bold text-purple-600 dark:text-purple-400 ms-1"
                        x-text="totalAsignadoDestinos() + ' pzas'"></span>
                </div>
                <div>
                    <span class="text-gray-500 dark:text-gray-400">Origen restante:</span>
                    <span
                        :class="remanenteOrigen() < 0 ? 'text-red-600 font-bold' :
                            'text-green-600 dark:text-green-400 font-bold'"
                        class="ms-1"
                        x-text="remanenteOrigen() + ' pzas'"></span>
                </div>
            </div>
        </div>
    </template>

    {{-- ========================================================= --}}
    {{-- PASO 1: Seleccion de ventas destino y cantidades --}}
    {{-- ========================================================= --}}
    <div x-show="paso === 1" class="space-y-3">
        {{-- Buscador --}}
        <div class="relative">
            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                </svg>
            </div>
            <input type="text" x-on:input="buscar($event)"
                class="w-full block py-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                placeholder="Buscar por Nombre, No. de socio o Folio">
        </div>

        {{-- Tabla de ventas abiertas --}}
        <div class="max-h-60 overflow-y-auto shadow-sm border border-gray-200 rounded-lg dark:border-gray-700">
            <table class="w-full text-sm text-left rtl:text-right text-gray-600 dark:text-gray-300">
                <thead
                    class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-300 sticky top-0 z-10">
                    <tr>
                        <th scope="col" class="px-3.5 py-2.5 w-24">Folio</th>
                        <th scope="col" class="px-3.5 py-2.5">Cliente / Socio</th>
                        <th scope="col" class="px-3.5 py-2.5 w-28">Total</th>
                        <th scope="col" class="px-3.5 py-2.5 w-36 text-center">Cant. a Enviar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="(v, i) in ventas_mostradas" :key="v.folio">
                        <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700">
                            <td class="px-3.5 py-2 font-bold text-gray-900 dark:text-white" x-text="'#' + v.folio"></td>
                            <td class="px-3.5 py-2">
                                <span class="font-medium text-gray-900 dark:text-white"
                                    x-text="v.nombre || 'Público General'"></span>
                                <template x-if="v.id_socio">
                                    <span class="text-xs text-gray-400 ms-1" x-text="'(' + v.id_socio + ')'"></span>
                                </template>
                            </td>
                            <td class="px-3.5 py-2 font-semibold text-gray-900 dark:text-white" x-text="'$' + Number(v.total).toFixed(2)"></td>
                            <td class="px-3.5 py-2 text-center">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <button type="button" x-on:click="if (v.cantidad_destino > 0) v.cantidad_destino--"
                                        class="w-7 h-7 text-gray-600 bg-gray-200 rounded hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 font-bold text-sm leading-none">-</button>
                                    <input type="number" min="0" :max="producto ? producto.cantidad : 100"
                                        step="1" x-model.number="v.cantidad_destino"
                                        class="w-14 p-1 text-center font-bold text-sm text-gray-900 border border-gray-300 rounded bg-white focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <button type="button"
                                        x-on:click="if (producto && totalAsignadoDestinos() < producto.cantidad) v.cantidad_destino++"
                                        class="w-7 h-7 text-gray-600 bg-gray-200 rounded hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 font-bold text-sm leading-none">+</button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="ventas_mostradas.length === 0">
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-400">No se encontraron otras
                                ventas abiertas en este corte.</td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Footer Paso 1 --}}
        <div class="flex items-center justify-end pt-2 border-t border-gray-200 dark:border-gray-700">
            <button type="button" x-on:click="continuarPaso1()"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700">
                <span
                    x-text="(modificadores_capturados.length > 0 && totalAsignadoDestinos() < (producto ? producto.cantidad : 0)) ? 'Continuar a Modificadores →' : 'Confirmar Transferencia'"></span>
            </button>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- PASO 2: Distribucion de modificadores (Diseno Optimizado) --}}
    {{-- ========================================================= --}}
    <div x-show="paso === 2" class="space-y-2.5">
        <div class="text-xs text-gray-600 dark:text-gray-400 flex items-center justify-between">
            <span>Asigne los modificadores correspondientes. La columna <strong>Sin Asignar</strong> debe quedar en
                0.</span>
            <span class="text-gray-400">Las reglas de cada grupo se muestran en la cabecera de cada columna.</span>
        </div>

        {{-- Matriz de distribucion con reglas integradas en la cabecera --}}
        <div class="max-h-64 overflow-auto shadow-sm border border-gray-200 rounded-lg dark:border-gray-700">
            <table class="w-full text-sm text-left rtl:text-right text-gray-600 dark:text-gray-300">
                <thead
                    class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-300 sticky top-0 z-10">
                    <tr>
                        <th scope="col" class="px-3 py-2 min-w-40 align-top">
                            <span>Modificador / Grupo</span>
                        </th>
                        <th scope="col" class="px-2 py-2 text-center w-16 align-top">
                            <span>Disp.</span>
                        </th>
                        {{-- Cabecera por cada venta con sus badges de reglas integrados --}}
                        <template x-for="vp in ventas_participantes" :key="vp.folio">
                            <th scope="col"
                                class="px-2.5 py-1.5 text-center min-w-36 max-w-48 border-x dark:border-gray-600 align-top">
                                <div class="font-bold text-xs text-gray-900 dark:text-white truncate" :title="vp.titulo"
                                    x-text="vp.titulo"></div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 font-semibold mb-1"
                                    x-text="vp.cantidad + ' pza(s)'"></div>
                                {{-- Badges de validacion integrados directamente en la cabecera --}}
                                <div class="flex flex-col gap-0.5">
                                    <template x-for="grupo in grupos_reglas" :key="grupo.id_grupo">
                                        <span
                                            :class="totalAsignadoGrupo(vp, grupo.id_grupo) > (grupo.modif_maximos * vp
                                                    .cantidad) ?
                                                'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300' :
                                                (grupo.forzar_captura && totalAsignadoGrupo(vp, grupo.id_grupo) < vp
                                                    .cantidad ?
                                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' :
                                                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300')"
                                            class="px-1.5 py-0.5 rounded text-[11px] font-normal leading-snug text-center">
                                            <span
                                                x-text="grupo.descripcion + ': ' + totalAsignadoGrupo(vp, grupo.id_grupo) + '/' + (grupo.modif_maximos * vp.cantidad)"></span>
                                            <span class="text-[10px] opacity-75 ms-0.5"
                                                x-text="'(Inc: ' + (grupo.modif_incluidos * vp.cantidad) + ')'"></span>
                                        </span>
                                    </template>
                                </div>
                            </th>
                        </template>
                        <th scope="col" class="px-2.5 py-2 text-center w-24 align-top">
                            <span>Sin Asignar</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="mod in modificadores_capturados" :key="mod.clave_producto">
                        <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700">
                            <td class="px-3 py-2 font-medium text-gray-900 dark:text-white">
                                <div class="font-medium text-sm" x-text="mod.nombre"></div>
                                <div class="text-xs text-gray-400"
                                    x-text="'(' + obtenerNombreGrupo(mod.id_grupo) + ')'"></div>
                            </td>
                            <td class="px-2 py-2 text-center font-bold text-sm text-gray-900 dark:text-white"
                                x-text="mod.cantidad"></td>
                            {{-- Controles de asignacion por venta --}}
                            <template x-for="vp in ventas_participantes" :key="vp.folio">
                                <td class="px-2.5 py-2 text-center border-x dark:border-gray-700">
                                    <div class="flex items-center justify-center space-x-1.5">
                                        <button type="button"
                                            x-on:click="if (vp.asignaciones[mod.clave_producto] > 0) vp.asignaciones[mod.clave_producto]--"
                                            class="w-6 h-6 text-gray-600 bg-gray-200 rounded hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 font-bold text-sm leading-none">-</button>
                                        <input type="number" min="0" :max="mod.cantidad" step="1"
                                            x-model.number="vp.asignaciones[mod.clave_producto]"
                                            class="w-12 p-1 text-center font-bold text-sm text-gray-900 border border-gray-300 rounded bg-white focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <button type="button" x-on:click="sumarModificador(vp, mod)"
                                            :class="!puedeSumarModificador(vp, mod) ? 'opacity-40 cursor-not-allowed' : ''"
                                            class="w-6 h-6 text-gray-600 bg-gray-200 rounded hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 font-bold text-sm leading-none">+</button>
                                    </div>
                                </td>
                            </template>
                            <td class="px-2.5 py-2 text-center font-bold">
                                <span
                                    :class="sinAsignarModificador(mod) === 0 ?
                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' :
                                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'"
                                    class="px-2.5 py-1 rounded text-xs inline-block"
                                    x-text="sinAsignarModificador(mod)"></span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Footer Paso 2 (Siempre visible) --}}
        <div class="flex items-center justify-between pt-2.5 border-t border-gray-200 dark:border-gray-700">
            <button type="button" x-on:click="paso = 1"
                class="px-3.5 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                ← Volver a cantidades
            </button>
            <button type="button" x-on:click="confirmarPaso2()"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700">
                Confirmar Transferencia
            </button>
        </div>
    </div>

</div>
