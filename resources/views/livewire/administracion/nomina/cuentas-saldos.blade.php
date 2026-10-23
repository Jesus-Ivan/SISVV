<div class="space-y-6">
    <!-- Mensajes de Notificación -->
    @if ($successMessage)
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 flex justify-between items-center shadow-sm">
            <div class="flex items-center">
                <svg class="w-4 h-4 me-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                </svg>
                <span>{{ $successMessage }}</span>
            </div>
            <button wire:click="$set('successMessage', '')" class="text-green-800 dark:text-green-400 font-bold">&times;</button>
        </div>
    @endif

    <!-- Tarjetas de Métricas / KPIs de Saldos -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Total Pendiente -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Saldo Total Pendiente</span>
                    <div class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">
                        ${{ number_format($totalSaldoPendiente, 2) }}
                    </div>
                </div>
                <div class="p-3 bg-rose-50 text-rose-600 rounded-xl dark:bg-rose-900/40 dark:text-rose-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Deuda acumulada por cobrar a empleados</p>
        </div>

        <!-- Adelantos Entregados Este Mes -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Adelantos Otorgados (Mes)</span>
                    <div class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">
                        ${{ number_format($adelantosEsteMes, 2) }}
                    </div>
                </div>
                <div class="p-3 bg-amber-50 text-amber-600 rounded-xl dark:bg-amber-900/40 dark:text-amber-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Efectivo entregado como anticipo</p>
        </div>

        <!-- Recuperado / Descontado Este Mes -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amortizado en Nómina (Mes)</span>
                    <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">
                        ${{ number_format($descontadoEnNominaMes, 2) }}
                    </div>
                </div>
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl dark:bg-emerald-900/40 dark:text-emerald-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- Botón Registrar Adelanto / Abono -->
                <button wire:click="openMovimientoModal()" type="button"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Registrar Adelanto / Abono
                </button>

                <!-- Exportar a Excel -->
                <a href="{{ route('nomina.excel.cuentas', ['tipo_empleado' => $tipo_empleado]) }}" target="_blank"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-emerald-800 bg-emerald-50 border border-emerald-300 rounded-lg hover:bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Exportar Cuentas a Excel
                </a>
            </div>
        </div>

        <!-- Filtros Secundarios -->
        <div class="flex flex-wrap items-center gap-4 mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 text-xs">
            <div class="flex items-center gap-2">
                <span class="font-semibold text-gray-500 dark:text-gray-400">Tipo:</span>
                <select wire:model.live="tipo_empleado" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
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
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
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
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
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
                                <span class="px-2 py-0.5 text-xs font-semibold rounded {{ $emp->tipo_empleado === 'PLANTA' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300' }}">
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

    <!-- MODAL REGISTRAR MOVIMIENTO (Adelanto / Abono en Efectivo) -->
    @if ($showMovimientoModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        Registrar Movimiento de Cuenta
                    </h3>
                    <button wire:click="$set('showMovimientoModal', false)" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <form wire:submit="registrarMovimiento" class="space-y-4">
                    <!-- Seleccionar Empleado -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Empleado *</label>
                        <select wire:model.live="empleadoIdSeleccionado"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Selecciona un empleado...</option>
                            @foreach ($todosEmpleadosActivos as $e)
                                <option value="{{ $e->id }}">#{{ $e->id }} - {{ $e->nombre_completo }} (Saldo: ${{ number_format($e->saldo_adelantos, 2) }})</option>
                            @endforeach
                        </select>
                        @error('empleadoIdSeleccionado') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    @if ($empleadoSeleccionado)
                        <div class="p-2.5 bg-blue-50 rounded-lg text-xs text-blue-900 dark:bg-blue-900/30 dark:text-blue-300">
                            Saldo Actual: <strong>${{ number_format($empleadoSeleccionado->saldo_adelantos, 2) }}</strong>
                        </div>
                    @endif

                    <!-- Tipo de Movimiento -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Tipo de Movimiento *</label>
                        <select wire:model="tipo_movimiento"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="ADELANTO_EFECTIVO">Adelanto en Efectivo (Aumenta Deuda)</option>
                            <option value="ABONO_DIRECTO">Abono Directo en Efectivo (Disminuye Deuda)</option>
                            <option value="AJUSTE">Ajuste Manual de Saldo</option>
                        </select>
                    </div>

                    <!-- Monto y Fecha -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Monto ($) *</label>
                            <input type="number" step="0.01" wire:model="monto"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="0.00">
                            @error('monto') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Fecha *</label>
                            <input type="date" wire:model="fecha"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @error('fecha') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Referencia / Motivo -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Referencia / Motivo</label>
                        <input type="text" wire:model="referencia"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="Ej. Anticipo solicitado para gastos personales">
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" wire:click="$set('showMovimientoModal', false)"
                            class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
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
    @endif

    <!-- MODAL HISTORIAL / KARDEX DE CUENTA -->
    @if ($showHistorialModal && $historialEmpleado)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="relative w-full max-w-3xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            Kardex de Cuenta: #{{ $historialEmpleado->id }} - {{ $historialEmpleado->nombre_completo }}
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $historialEmpleado->puesto?->nombre }} &bull; Saldo Actual: <strong class="text-rose-600">${{ number_format($historialEmpleado->saldo_adelantos, 2) }}</strong>
                        </p>
                    </div>
                    <button wire:click="$set('showHistorialModal', false)" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <div class="max-h-[60vh] overflow-y-auto">
                    <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-[11px] text-gray-700 uppercase bg-gray-50 dark:bg-gray-750 dark:text-gray-300 sticky top-0">
                            <tr>
                                <th class="p-2.5">Fecha</th>
                                <th class="p-2.5">Tipo Movimiento</th>
                                <th class="p-2.5">Referencia</th>
                                <th class="p-2.5 text-right">Monto</th>
                                <th class="p-2.5 text-right">Saldo Ant.</th>
                                <th class="p-2.5 text-right">Saldo Nvo.</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($historialMovimientos as $mov)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="p-2.5">{{ $mov->fecha ? $mov->fecha->format('d/m/Y') : '' }}</td>
                                    <td class="p-2.5">
                                        <span class="px-2 py-0.5 rounded font-semibold {{ $mov->naturaleza === 'CARGO' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                            {{ $mov->tipo_movimiento }}
                                        </span>
                                    </td>
                                    <td class="p-2.5">{{ $mov->referencia ?: '-' }}</td>
                                    <td class="p-2.5 text-right font-bold {{ $mov->naturaleza === 'CARGO' ? 'text-rose-600' : 'text-emerald-600' }}">
                                        {{ $mov->naturaleza === 'CARGO' ? '+' : '-' }}${{ number_format($mov->monto, 2) }}
                                    </td>
                                    <td class="p-2.5 text-right text-gray-400">${{ number_format($mov->saldo_anterior, 2) }}</td>
                                    <td class="p-2.5 text-right font-bold text-gray-800 dark:text-white">${{ number_format($mov->saldo_nuevo, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-4 text-center text-gray-400">
                                        No hay movimientos registrados para este empleado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700 mt-4">
                    <button wire:click="$set('showHistorialModal', false)"
                        class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

