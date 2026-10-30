<div class="space-y-6">
    {{-- Estilos globales para inputs number: ocultar flechas/spinners y prevenir scroll accidental en todas las 4 secciones --}}
    <style>
        input[type=number]::-webkit-outer-spin-button,
        input[type=number]::-webkit-inner-spin-button {
            -webkit-appearance: none !important;
            margin: 0 !important;
        }
        input[type=number] {
            -moz-appearance: textfield !important;
            appearance: textfield !important;
        }
    </style>

    <script>
        (function() {
            if (!window.__wheelNumberInputListenerAdded) {
                window.__wheelNumberInputListenerAdded = true;
                window.addEventListener('wheel', function(e) {
                    if (document.activeElement && document.activeElement.type === 'number') {
                        document.activeElement.blur();
                    }
                }, { passive: true });
            }
        })();
    </script>

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

    <!-- Contenedor Flotante de Notificaciones / Alerts (Esquina inferior izquierda) -->
    <div x-data="{
            toasts: [],
            add(message, type = 'success') {
                const id = 'toast_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6);
                this.toasts.push({ id, message, type });
                setTimeout(() => {
                    this.remove(id);
                }, 5000);
            },
            remove(id) {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }
        }"
        x-on:toast.window="
            let data = $event.detail;
            if (typeof data === 'string') {
                add(data, 'success');
            } else if (Array.isArray(data) && data[0]) {
                add(data[0].message || data[0], data[0].type || 'success');
            } else if (data && typeof data === 'object') {
                add(data.message || '', data.type || 'success');
            }
        "
        class="fixed bottom-5 left-5 z-50 flex flex-col-reverse gap-2.5 max-w-sm w-full pointer-events-none"
        wire:ignore>
        
        <template x-for="t in toasts" :key="t.id">
            <div x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-4 -translate-x-4 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 translate-x-0 scale-100"
                 x-transition:leave="transition ease-in duration-250 transform"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 -translate-x-8 scale-90"
                 class="pointer-events-auto relative overflow-hidden bg-white dark:bg-gray-800 rounded-xl shadow-2xl border transition-all duration-200"
                 :class="{
                    'border-emerald-200 dark:border-emerald-800/60 ring-1 ring-emerald-500/20': t.type === 'success',
                    'border-red-200 dark:border-red-800/60 ring-1 ring-red-500/20': t.type === 'error',
                    'border-blue-200 dark:border-blue-800/60 ring-1 ring-blue-500/20': t.type === 'info',
                    'border-amber-200 dark:border-amber-800/60 ring-1 ring-amber-500/20': t.type === 'warning'
                 }">
                
                <!-- Barra de progreso de 5 segundos -->
                <div class="h-1 w-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                    <div class="h-1"
                         :class="{
                            'bg-emerald-500': t.type === 'success',
                            'bg-red-500': t.type === 'error',
                            'bg-blue-500': t.type === 'info',
                            'bg-amber-500': t.type === 'warning'
                         }"
                         style="animation: toastProgressBar 5s linear forwards;"></div>
                </div>

                <div class="p-3.5 flex items-start gap-3">
                    <!-- Icono según tipo -->
                    <template x-if="t.type === 'success'">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </template>
                    <template x-if="t.type === 'error'">
                        <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600 dark:bg-red-900/50 dark:text-red-400 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                    </template>
                    <template x-if="t.type === 'info' || t.type === 'warning'">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-400 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </template>

                    <!-- Contenido del texto -->
                    <div class="flex-1 min-w-0 pr-1">
                        <p class="text-[11px] font-bold uppercase tracking-wider"
                           :class="{
                                'text-emerald-700 dark:text-emerald-400': t.type === 'success',
                                'text-red-700 dark:text-red-400': t.type === 'error',
                                'text-blue-700 dark:text-blue-400': t.type === 'info',
                                'text-amber-700 dark:text-amber-400': t.type === 'warning'
                           }"
                           x-text="t.type === 'success' ? 'Éxito' : (t.type === 'error' ? 'Atención' : 'Notificación')"></p>
                        <p class="text-xs font-medium text-gray-800 dark:text-gray-200 mt-0.5 leading-snug break-words" x-text="t.message"></p>
                    </div>

                    <!-- Botón Cerrar -->
                    <button type="button" @click="remove(t.id)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </template>
    </div>

    <style>
        @keyframes toastProgressBar {
            0% { width: 100%; }
            100% { width: 0%; }
        }
    </style>
</div>

