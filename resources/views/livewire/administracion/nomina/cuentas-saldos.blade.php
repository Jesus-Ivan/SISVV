<div x-data x-on:abrir-pdf.window="window.abrirDocumentoPdf ? window.abrirDocumentoPdf($event.detail) : null">
    <div class="space-y-6">
        <!-- Tarjetas de Métricas / KPIs de Saldos -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Total Pendiente -->
            <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Saldo
                            Total Pendiente</span>
                        <div class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">
                            ${{ number_format($totalSaldoPendiente, 2) }}
                        </div>
                    </div>
                    <div class="p-3 bg-rose-50 text-rose-600 rounded-xl dark:bg-rose-900/40 dark:text-rose-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-2">Deuda acumulada por cobrar a empleados</p>
            </div>

            <!-- Adelantos Entregados Este Mes -->
            <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <span
                            class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Adelantos
                            Otorgados (Mes)</span>
                        <div class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">
                            ${{ number_format($adelantosEsteMes, 2) }}
                        </div>
                    </div>
                    <div class="p-3 bg-amber-50 text-amber-600 rounded-xl dark:bg-amber-900/40 dark:text-amber-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-2">Efectivo entregado como anticipo</p>
            </div>

            <!-- Recuperado / Descontado Este Mes -->
            <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <span
                            class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amortizado
                            en Nómina (Mes)</span>
                        <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">
                            ${{ number_format($descontadoEnNominaMes, 2) }}
                        </div>
                    </div>
                    <div
                        class="p-3 bg-emerald-50 text-emerald-600 rounded-xl dark:bg-emerald-900/40 dark:text-emerald-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-2">Descuentos aplicados directamente en recibos</p>
            </div>
        </div>

        <!-- Barra de Búsqueda, Filtros y Acciones -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">

                <!-- Barra de Búsqueda -->
                <div class="relative flex-1 max-w-md">
                    <input wire:model.live.debounce.300ms="search" type="text"
                        class="block w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                        placeholder="Buscar por ID, nombre o apellidos...">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Exportar a Excel -->
                    <button type="button" wire:click="openReporteExcelModal"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-emerald-800 bg-emerald-50 border border-emerald-300 rounded-lg hover:bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-800 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Exportar a Excel
                    </button>
                </div>
            </div>

            <!-- Filtros Secundarios -->
            <div
                class="flex flex-wrap items-center gap-4 mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 text-xs">
                <div class="flex items-center gap-2">
                    <span class="font-semibold text-gray-500 dark:text-gray-400">Tipo:</span>
                    <select wire:model.live="tipo_empleado"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="TODOS">Todos</option>
                        <option value="PLANTA">De Planta</option>
                        <option value="TEMPORAL">Temporales</option>
                    </select>
                </div>

                <label class="flex items-center gap-2 cursor-pointer text-gray-700 dark:text-gray-300">
                    <input type="checkbox" wire:model.live="soloConSaldo" class="w-4 h-4 text-blue-600 rounded">
                    <span>Mostrar únicamente empleados con saldo pendiente (&gt; $0)</span>
                </label>
            </div>
        </div>

        <!-- Tabla de Cuentas de Empleados -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead
                        class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th scope="col" class="px-4 py-3.5 w-16 text-center">ID</th>
                            <th scope="col" class="px-4 py-3.5">Empleado</th>
                            <th scope="col" class="px-4 py-3.5">Puesto / Área</th>
                            <th scope="col" class="px-4 py-3.5 text-center">Tipo</th>
                            <th scope="col" class="px-4 py-3.5 text-right">Saldo Pendiente</th>
                            <th scope="col" class="px-4 py-3.5 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($empleados as $emp)
                            <tr wire:key="emp-cuenta-{{ $emp->id }}"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <td class="px-4 py-3 text-center font-bold text-gray-900 dark:text-white">
                                    #{{ $emp->id }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-gray-900 dark:text-white">
                                        {{ $emp->nombre_completo }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $emp->activo ? 'Activo' : 'Inactivo / Baja' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-gray-900 dark:text-white">
                                        {{ $emp->puesto?->nombre ?: 'Sin puesto' }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $emp->area?->nombre ?: 'Sin área' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="px-2 py-0.5 text-xs font-semibold rounded {{ $emp->tipo_empleado === 'PLANTA' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300' }}">
                                        {{ $emp->tipo_empleado }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if ($emp->saldo_adelantos > 0)
                                        <span class="text-base font-bold text-rose-600 dark:text-rose-400">
                                            ${{ number_format($emp->saldo_adelantos, 2) }}
                                        </span>
                                    @else
                                        <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                                            Al corriente ($0.00)
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="inline-flex items-center gap-2">
                                        <!-- Registrar Movimiento Directo -->
                                        <button wire:click="openMovimientoModal({{ $emp->id }})"
                                            class="px-2.5 py-1 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg dark:bg-blue-900/30 dark:text-blue-300 dark:hover:bg-blue-900/50 transition"
                                            title="Registrar adelanto o abono en efectivo">
                                            + Movimiento
                                        </button>

                                        <!-- Ver Historial / Kardex -->
                                        <button wire:click="verHistorial({{ $emp->id }})"
                                            class="px-2.5 py-1 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition"
                                            title="Ver estado de cuenta e historial">
                                            Ver Kardex
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    No se encontraron registros de cuentas con los filtros actuales.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($empleados->hasPages())
                <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $empleados->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- MODAL REGISTRAR MOVIMIENTO (Adelanto / Abono en Efectivo) -->
    @if ($showMovimientoModal)
        <div class="fixed inset-0 z-50 overflow-y-auto m-0" role="dialog" aria-modal="true">
            <!-- Backdrop fijo (cubre el 100% de la pantalla) -->
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity m-0"
                wire:click="$set('showMovimientoModal', false)" aria-hidden="true"></div>

            <!-- Contenedor centrado para scroll -->
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 m-0"
                wire:click.self="$set('showMovimientoModal', false)">
                <div class="relative w-full max-w-md my-6 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 p-6 text-left overflow-hidden transform transition-all"
                    @click.stop>
                    <div
                        class="flex items-center justify-between pb-3 mb-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            Registrar Movimiento de Cuenta
                        </h3>
                        <button wire:click="$set('showMovimientoModal', false)"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xl font-bold">&times;</button>
                    </div>

                    <form wire:submit="registrarMovimiento" class="space-y-4">
                        <!-- Empleado (Fijo / Deshabilitado para evitar modificaciones accidentales) -->
                        <div>
                            <label
                                class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Empleado</label>
                            @if ($empleadoSeleccionado)
                                <div
                                    class="p-3 bg-gray-50 dark:bg-gray-750 border border-gray-200 dark:border-gray-600 rounded-lg flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">
                                            #{{ $empleadoSeleccionado->id }} -
                                            {{ $empleadoSeleccionado->nombre_completo }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $empleadoSeleccionado->puesto?->nombre ?? 'Sin puesto asignado' }}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span
                                            class="text-[10px] uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400 block">Saldo
                                            Actual</span>
                                        <span class="text-sm font-black text-rose-600 dark:text-rose-400">
                                            ${{ number_format($empleadoSeleccionado->saldo_adelantos, 2) }}
                                        </span>
                                    </div>
                                </div>
                            @else
                                <div
                                    class="p-3 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-500 text-sm rounded-lg">
                                    No hay empleado seleccionado.
                                </div>
                            @endif
                            @error('empleadoIdSeleccionado')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Tipo de Movimiento -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Tipo
                                de Movimiento *</label>
                            <select wire:model.live="tipo_movimiento"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="ADELANTO_EFECTIVO">Adelanto en Efectivo (Aumenta Deuda)</option>
                                <option value="ABONO_DIRECTO">Abono Directo en Efectivo (Disminuye Deuda)</option>
                                <option value="AJUSTE">Ajuste Manual de Saldo</option>
                            </select>
                            @if ($tipo_movimiento === 'AJUSTE')
                                <p class="text-[11px] text-amber-600 dark:text-amber-400 mt-1">
                                    Captura el nuevo saldo objetivo que debe tener el empleado. El sistema calculará el
                                    cargo o abono necesario.
                                </p>
                            @endif
                        </div>

                        <!-- Monto y Fecha -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">
                                    {{ $tipo_movimiento === 'AJUSTE' ? 'Nuevo Saldo Objetivo ($) *' : 'Monto ($) *' }}
                                </label>
                                @if ($tipo_movimiento === 'ABONO_DIRECTO')
                                    <span class="text-[10px] text-amber-600 dark:text-amber-400 block font-normal -mt-0.5 mb-1">
                                        Máximo a liquidar: ${{ number_format($empleadoSeleccionado?->saldo_adelantos ?? 0, 2) }}
                                    </span>
                                @endif
                                <input type="number" step="0.01" wire:model="monto"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="0.00">
                                @error('monto')
                                    <span class="text-xs text-red-500">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Fecha
                                    *</label>
                                @if ($tipo_movimiento === 'AJUSTE')
                                    <span class="text-[10px] text-gray-500 dark:text-gray-400 block font-normal -mt-0.5 mb-1">
                                        Fijada a hoy (corte contable)
                                    </span>
                                @endif
                                <input type="date" wire:model="fecha"
                                    @if ($tipo_movimiento === 'AJUSTE') disabled class="bg-gray-100 border border-gray-300 text-gray-500 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed dark:bg-gray-750 dark:border-gray-600 dark:text-gray-400"
                                    @else class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" @endif>
                                @error('fecha')
                                    <span class="text-xs text-red-500">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Referencia / Motivo -->
                        <div>
                            <label
                                class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Referencia
                                / Motivo</label>
                            <input type="text" wire:model="referencia"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Ej. Anticipo solicitado para gastos personales">
                        </div>

                        <div class="flex justify-end gap-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" wire:click="$set('showMovimientoModal', false)"
                                class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                Registrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL HISTORIAL / KARDEX DE CUENTA -->
    @if ($showHistorialModal && $historialEmpleado)
        <div class="fixed inset-0 z-50 overflow-y-auto m-0" role="dialog" aria-modal="true">
            <!-- Backdrop fijo (cubre el 100% de la pantalla) -->
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity m-0"
                wire:click="$set('showHistorialModal', false)" aria-hidden="true"></div>

            <!-- Contenedor centrado para scroll -->
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 m-0"
                wire:click.self="$set('showHistorialModal', false)">
                <div class="relative w-full max-w-3xl my-6 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 p-6 text-left overflow-hidden transform transition-all"
                    @click.stop>
                    <div
                        class="flex items-center justify-between pb-3 mb-4 border-b border-gray-200 dark:border-gray-700">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                    Kardex de Cuenta: #{{ $historialEmpleado->id }} -
                                    {{ $historialEmpleado->nombre_completo }}
                                </h3>
                                <button type="button" wire:click="reconciliarCuenta({{ $historialEmpleado->id }})"
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-semibold text-blue-700 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100 dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-800 transition"
                                    title="Recalcular y reconciliar la cadena de saldos de esta cuenta">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <span wire:loading.remove
                                        wire:target="reconciliarCuenta({{ $historialEmpleado->id }})">Reconciliar
                                        Saldos</span>
                                    <span wire:loading
                                        wire:target="reconciliarCuenta({{ $historialEmpleado->id }})">Sincronizando...</span>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                {{ $historialEmpleado->puesto?->nombre }} &bull; Saldo Actual: <strong
                                    class="text-rose-600">${{ number_format($historialEmpleado->saldo_adelantos, 2) }}</strong>
                            </p>
                        </div>
                        <button wire:click="$set('showHistorialModal', false)"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xl font-bold">&times;</button>
                    </div>

                    <!-- Barra de Filtros del Kardex -->
                    <div class="pb-3 mb-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-2.5 text-xs">
                            <!-- Búsqueda rápida por Folio / Referencia -->
                            <div class="sm:col-span-1">
                                <label
                                    class="block text-[10px] font-bold uppercase text-gray-500 dark:text-gray-400 mb-1">Buscar
                                    Folio / Ref.</label>
                                <input type="text" wire:model.live.debounce.350ms="kardexSearch"
                                    class="bg-white border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Folio o palabra clave...">
                            </div>

                            <!-- Tipo de Movimiento -->
                            <div class="sm:col-span-1">
                                <label
                                    class="block text-[10px] font-bold uppercase text-gray-500 dark:text-gray-400 mb-1">Tipo
                                    de Movimiento</label>
                                <select wire:model.live="kardexTipoMovimiento"
                                    class="bg-white border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="TODOS">Todos los tipos</option>
                                    <option value="ADELANTO_EFECTIVO">Adelanto Efectivo</option>
                                    <option value="ABONO_DIRECTO">Abono Directo</option>
                                    <option value="DESCUENTO_NOMINA">Descuento en Nómina</option>
                                    <option value="AJUSTE">Ajuste de Saldo</option>
                                </select>
                            </div>

                            <!-- Rango: Fecha Inicio -->
                            <div class="sm:col-span-1">
                                <label
                                    class="block text-[10px] font-bold uppercase text-gray-500 dark:text-gray-400 mb-1">Desde</label>
                                <input type="date" wire:model.live="kardexFechaInicio"
                                    class="bg-white border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <!-- Rango: Fecha Fin y Reset -->
                            <div class="sm:col-span-1 flex items-end gap-1.5">
                                <div class="flex-1">
                                    <label
                                        class="block text-[10px] font-bold uppercase text-gray-500 dark:text-gray-400 mb-1">Hasta</label>
                                    <input type="date" wire:model.live="kardexFechaFin"
                                        class="bg-white border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                @if ($kardexSearch !== '' || $kardexTipoMovimiento !== 'TODOS' || $kardexFechaInicio !== '' || $kardexFechaFin !== '')
                                    <button type="button" wire:click="limpiarFiltrosKardex"
                                        title="Limpiar filtros del Kardex"
                                        class="p-2 text-gray-500 hover:text-red-600 bg-white border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:text-red-400 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de movimientos -->
                    <div class="max-h-[60vh] overflow-y-auto">
                        <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-[11px] text-gray-700 uppercase bg-gray-50 dark:bg-gray-750 dark:text-gray-300 sticky top-0">
                                <tr>
                                    <th class="p-2.5">Folio / Fecha</th>
                                    <th class="p-2.5">Tipo Movimiento</th>
                                    <th class="p-2.5">Referencia</th>
                                    <th class="p-2.5 text-right">Monto</th>
                                    <th class="p-2.5 text-right">Saldo Ant.</th>
                                    <th class="p-2.5 text-right">Saldo Nvo.</th>
                                    <th class="p-2.5 text-center">Comprobante</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse ($historialMovimientos as $mov)
                                    <tr wire:key="mov-kardex-{{ $mov->id }}"
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="p-2.5">
                                            @if ($mov->folio)
                                                <span
                                                    class="font-mono font-bold text-gray-900 dark:text-gray-100 block">{{ $mov->folio }}</span>
                                            @endif
                                            <span
                                                class="text-gray-400 text-[11px]">{{ $mov->fecha ? $mov->fecha->format('d/m/Y') : '' }}</span>
                                        </td>
                                        <td class="p-2.5">
                                            <span
                                                class="px-2 py-0.5 rounded font-semibold {{ $mov->naturaleza === 'CARGO' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                                {{ $mov->tipo_movimiento }}
                                            </span>
                                        </td>
                                        <td class="p-2.5">{{ $mov->referencia ?: '-' }}</td>
                                        <td
                                            class="p-2.5 text-right font-bold {{ $mov->naturaleza === 'CARGO' ? 'text-rose-600' : 'text-emerald-600' }}">
                                            {{ $mov->naturaleza === 'CARGO' ? '+' : '-' }}${{ number_format($mov->monto, 2) }}
                                        </td>
                                        <td class="p-2.5 text-right text-gray-400">
                                            ${{ number_format($mov->saldo_anterior, 2) }}</td>
                                        <td class="p-2.5 text-right font-bold text-gray-800 dark:text-white">
                                            ${{ number_format($mov->saldo_nuevo, 2) }}</td>
                                        <td class="p-2.5 text-center">
                                            @if (in_array($mov->tipo_movimiento, ['ADELANTO_EFECTIVO', 'ABONO_DIRECTO', 'AJUSTE']))
                                                <a href="{{ route('nomina.pdf.movimiento', $mov->id) }}"
                                                    target="_blank"
                                                    class="inline-flex items-center justify-center p-1.5 text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800 dark:hover:bg-blue-900/50"
                                                    title="Imprimir Comprobante PDF">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                    </svg>
                                                </a>
                                            @else
                                                <span class="text-gray-300 dark:text-gray-600 text-xs">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="p-4 text-center text-gray-400">
                                            No hay movimientos que coincidan con los filtros para este empleado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Reporte Excel de Cuentas y Movimientos -->
    @if ($showReporteExcelModal)
        <div class="fixed inset-0 z-50 overflow-y-auto m-0" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity m-0"
                wire:click="$set('showReporteExcelModal', false)" aria-hidden="true"></div>

            <!-- Contenedor centrado -->
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 m-0"
                wire:click.self="$set('showReporteExcelModal', false)">
                <div class="relative w-full max-w-lg my-6 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 p-6 text-left overflow-visible transform transition-all"
                    @click.stop>
                    <div
                        class="flex items-center justify-between pb-3 mb-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-2.5">
                            <div
                                class="p-2 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                    Reporte de Cuentas y Movimientos a Excel
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Balanza de saldos y libro detallado
                                    de adelantos y abonos</p>
                            </div>
                        </div>
                        <button wire:click="$set('showReporteExcelModal', false)"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xl font-bold">&times;</button>
                    </div>

                    <div class="space-y-4 text-xs">
                        <!-- Selector de Empleado (Opcional) -->
                        <div class="relative" x-data="{ open: false }">
                            <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">
                                Empleado (Opcional - Dejar vacío para todos)
                            </label>
                            @if ($exportEmpleadoNombre)
                                <div
                                    class="flex items-center justify-between p-2.5 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg">
                                    <span
                                        class="font-bold text-blue-900 dark:text-blue-200 text-xs">{{ $exportEmpleadoNombre }}</span>
                                    <button type="button" wire:click="limpiarExportEmpleado"
                                        class="text-xs text-rose-600 hover:text-rose-800 font-semibold p-1">
                                        &times; Quitar
                                    </button>
                                </div>
                            @else
                                <input type="text" wire:model.live.debounce.350ms="exportEmpleadoSearch"
                                    @focus="open = true" placeholder="Buscar empleado por ID o nombre (opcional)..."
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">

                                @if (count($empleadosExportBusqueda) > 0)
                                    <div class="absolute z-50 left-0 right-0 mt-1 max-h-48 overflow-y-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xl"
                                        @click.outside="open = false">
                                        @foreach ($empleadosExportBusqueda as $emp)
                                            <button type="button" wire:key="exp-emp-{{ $emp->id }}"
                                                wire:click="seleccionarExportEmpleado({{ $emp->id }}, '{{ addslashes($emp->nombre_completo) }}')"
                                                @click="open = false"
                                                class="w-full text-left p-2.5 hover:bg-blue-50 dark:hover:bg-gray-700 text-xs border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                                                <div>
                                                    <span
                                                        class="font-bold text-gray-900 dark:text-white">#{{ $emp->id }}
                                                        - {{ $emp->nombre_completo }}</span>
                                                    <span
                                                        class="text-[11px] text-gray-400 block">{{ $emp->puesto?->nombre }}
                                                        &bull; {{ $emp->area?->nombre }}</span>
                                                </div>
                                                <span
                                                    class="text-xs font-mono font-bold {{ $emp->saldo_adelantos > 0 ? 'text-rose-600' : 'text-gray-400' }}">
                                                    ${{ number_format($emp->saldo_adelantos, 2) }}
                                                </span>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            @endif
                        </div>

                        <!-- Tipo de Empleado y Tipo de Movimiento -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label
                                    class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Tipo
                                    de Empleado</label>
                                <select wire:model.live="exportTipoEmpleado"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="TODOS">Todos (Planta y Temporales)</option>
                                    <option value="PLANTA">De Planta</option>
                                    <option value="TEMPORAL">Temporales</option>
                                </select>
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Tipo
                                    de Movimiento</label>
                                <select wire:model.live="exportTipoMovimiento"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="TODOS">Todos los movimientos</option>
                                    <option value="ADELANTO_EFECTIVO">Adelanto en Efectivo</option>
                                    <option value="ABONO_DIRECTO">Abono Directo en Efectivo</option>
                                    <option value="DESCUENTO_NOMINA">Amortización / Descuento Nómina</option>
                                    <option value="AJUSTE">Ajuste de Saldo</option>
                                </select>
                            </div>
                        </div>

                        <!-- Rango de Fechas (Opcional - vacío por defecto) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label
                                    class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Fecha
                                    Desde (Opcional)</label>
                                <input type="date" wire:model.live="exportFechaInicio"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Fecha
                                    Hasta (Opcional)</label>
                                <input type="date" wire:model.live="exportFechaFin"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Footer / Acciones -->
                    <div
                        class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700 mt-5">
                        <button type="button" wire:click="$set('showReporteExcelModal', false)"
                            class="px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                            Cerrar
                        </button>

                        <button type="button" wire:click="descargarReporteExcel"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg wire:loading.remove wire:target="descargarReporteExcel" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <svg wire:loading wire:target="descargarReporteExcel" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="descargarReporteExcel">Descargar Reporte Excel</span>
                            <span wire:loading wire:target="descargarReporteExcel">Generando reporte...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
