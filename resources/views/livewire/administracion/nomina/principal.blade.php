<div class="space-y-6">
    <!-- Encabezado Principal del Módulo -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight flex items-center gap-3">
                    <span class="p-2.5 bg-gradient-to-tr from-blue-600 to-indigo-600 text-white rounded-xl shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </span>
                    Control y Libro de Nóminas
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Gestión integral de empleados, dispersión por períodos, recibos PDF media carta y seguimiento contable de adelantos en efectivo.
                </p>
            </div>
        </div>

        <!-- Pestañas de Navegación del Módulo -->
        <div class="flex flex-wrap gap-2 mt-6 border-b border-gray-200 dark:border-gray-700">
            <!-- Pestaña 1: Dispersión de Nómina -->
            <button wire:click="setTab('dispersion')" type="button"
                class="inline-flex items-center gap-2 px-4 py-3 text-sm font-bold border-b-2 transition {{ $tab === 'dispersion' ? 'border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Dispersión y Recibos
            </button>

            <!-- Pestaña 2: Empleados -->
            <button wire:click="setTab('empleados')" type="button"
                class="inline-flex items-center gap-2 px-4 py-3 text-sm font-bold border-b-2 transition {{ $tab === 'empleados' ? 'border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Directorio de Empleados
            </button>

            <!-- Pestaña 3: Cuentas y Saldos -->
            <button wire:click="setTab('cuentas')" type="button"
                class="inline-flex items-center gap-2 px-4 py-3 text-sm font-bold border-b-2 transition {{ $tab === 'cuentas' ? 'border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Cuentas y Saldos de Efectivo
            </button>

            <!-- Pestaña 4: Catálogos -->
            <button wire:click="setTab('catalogos')" type="button"
                class="inline-flex items-center gap-2 px-4 py-3 text-sm font-bold border-b-2 transition {{ $tab === 'catalogos' ? 'border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Catálogos (Áreas y Puestos)
            </button>
        </div>
    </div>

    <!-- Contenido Activo Según Pestaña -->
    <div>
        @if ($tab === 'dispersion')
            <livewire:administracion.nomina.dispersion wire:key="tab-dispersion" />
        @elseif ($tab === 'empleados')
            <livewire:administracion.nomina.empleados wire:key="tab-empleados" />
        @elseif ($tab === 'cuentas')
            <livewire:administracion.nomina.cuentas-saldos wire:key="tab-cuentas" />
        @elseif ($tab === 'catalogos')
            <livewire:administracion.nomina.catalogos wire:key="tab-catalogos" />
        @endif
    </div>
</div>

