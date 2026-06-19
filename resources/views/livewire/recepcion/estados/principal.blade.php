<div>
    {{-- FILA 1: búsqueda + toggle tarifa especial --}}
    <div class="m-2 flex items-center gap-3">
        <div class="relative flex-grow">
            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                </svg>
            </div>
            <input type="search" wire:model.live.debounce.500ms='search'
                class="block w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                placeholder="Nombre o número de socio" />
        </div>
        <div wire:loading>
            @include('livewire.utils.loading', ['w' => 5, 'h' => 5])
        </div>
        <button wire:click="toggleTarifaEspecial"
            class="border text-sm font-medium rounded-lg px-4 py-2.5 whitespace-nowrap transition"
            style="{{ $soloTarifaEspecial
                ? 'background-color:#2563eb; color:#ffffff; border-color:#2563eb;'
                : 'background-color:#ffffff; color:#1d4ed8; border-color:#3b82f6;' }}">
            Tarifa especial
        </button>
    </div>

    {{-- FILA 2: fechas + conceptos (pills) + vista --}}
    <div class="mx-2 mb-3 flex items-center gap-3 flex-wrap">
        {{-- Rango de fechas --}}
        <div class="flex items-center gap-2">
            <input type="date" wire:model.live.debounce.800ms="fechaInicio"
                class="text-sm border border-gray-300 rounded-lg p-2 bg-gray-50 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
            <span class="text-gray-400 text-sm">→</span>
            <input type="date" wire:model.live.debounce.800ms="fechaFin"
                class="text-sm border border-gray-300 rounded-lg p-2 bg-gray-50 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
        </div>
        <div class="h-5 w-px bg-gray-300 dark:bg-gray-600"></div>
        {{-- Conceptos como pills --}}
        <div class="flex gap-1">
            <button wire:click="setConceptos('T')"
                class="{{ $radioButon === 'T' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }} text-xs font-medium px-3 py-1.5 rounded-full transition">
                Todos
            </button>
            <button wire:click="setConceptos('P')"
                class="{{ $radioButon === 'P' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }} text-xs font-medium px-3 py-1.5 rounded-full transition">
                Pendientes
            </button>
            <button wire:click="setConceptos('C')"
                class="{{ $radioButon === 'C' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }} text-xs font-medium px-3 py-1.5 rounded-full transition">
                Consumos
            </button>
        </div>
        <div class="h-5 w-px bg-gray-300 dark:bg-gray-600"></div>
        {{-- Vista --}}
        <select wire:model.live.debounce.600ms='vista'
            class="text-sm border border-gray-300 rounded-lg p-2 bg-gray-50 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <option value="COM">Completa</option>
            <option value="ORD">Ordinaria</option>
            <option value="ESP">Especial</option>
        </select>
    </div>
    {{-- TABLA DE SOCIOS --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        SOCIO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        MEMBRESIA
                    </th>
                    <th scope="col" class="px-6 py-3">
                        ACCIONES
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->resultSocios as $index => $socio)
                    <tr wire:key={{ $index }}
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                        <td scope="row" class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <!-- IMAGEN DE PERFIL -->
                                <div>
                                    <img class="w-20 h-20 rounded-full" src="{{ asset($socio->img_path) }}"
                                        alt="Rounded avatar">
                                </div>
                                <!-- INFO -->
                                <div class="dark:text-white">
                                    <div class="flex items-center gap-2 font-medium">
                                        {{ $socio->nombre . ' ' . $socio->apellido_p . ' ' . $socio->apellido_m }}
                                        @if ($socio->tiene_tarifa_especial)
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100">Tarifa especial</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No.Socio:
                                        {{ $socio->id }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        <span>Correo 1: {{ $socio->correo1 ? $socio->correo1 : 'N/R' }}</span>
                                        <span>Correo 2: {{ $socio->correo2 ? $socio->correo2 : 'N/R' }}</span>
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $clavesMostradas = $socio->cuotasMembresia->isNotEmpty()
                                    ? $socio->cuotasMembresia->pluck('cuota.clave_membresia')->all()
                                    : array_filter([$socio->socioMembresia?->clave_membresia]);
                                //Estado real desde socios_membresias (fuente de verdad): una membresia
                                //en ANU que aun conserva su cuota mensual debe mostrarse como ANU, no MEN.
                                $estadosPorClave = $socio->socioMembresias->pluck('estado', 'clave_membresia');
                            @endphp
                            @forelse($socio->cuotasMembresia as $sc)
                                <div class="text-sm leading-5">
                                    <span class="text-gray-700 dark:text-gray-300">{{ $sc->cuota->clave_membresia }}</span>
                                    <span class="text-xs text-gray-400 ml-1">{{ $estadosPorClave[$sc->cuota->clave_membresia] ?? $sc->cuota->tipo }}</span>
                                </div>
                            @empty
                                <div>{{ $socio->socioMembresia ? $socio->socioMembresia->clave_membresia : 'N/R' }}</div>
                                <span class="text-xs text-gray-400">{{ $socio->socioMembresia ? $socio->socioMembresia->estado : '' }}</span>
                            @endforelse
                            {{-- Membresias en anualidad sin cargo fijo asociado (no aparecen en cuotasMembresia) --}}
                            @foreach ($socio->socioMembresias->where('estado', 'ANU')->whereNotIn('clave_membresia', $clavesMostradas) as $sm)
                                <div class="text-sm leading-5">
                                    <span class="text-gray-700 dark:text-gray-300">{{ $sm->clave_membresia }}</span>
                                    <span class="text-xs text-gray-400 ml-1">{{ $sm->estado }}</span>
                                </div>
                            @endforeach
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('recepcion.estado.nuevo', ['socio' => $socio->id]) }}"
                                class="text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="w-[24px] h-[24px]">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-width="3"
                                        d="M12 6h.01M12 12h.01M12 18h.01" />
                                </svg>
                                <span class="sr-only">Agregar concepto</span>
                            </a>
                            <a href="{{ route('recepcion.estado.reporte', ['socio' => $socio->id, 'tipo' => $radioButon, 'vista' => $vista, 'fInicio' => $fechaInicio, 'fFin' => $fechaFin, 'option' => 'd']) }}"
                                class="text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500">
                                <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd"
                                        d="M13 11.15V4a1 1 0 1 0-2 0v7.15L8.78 8.374a1 1 0 1 0-1.56 1.25l4 5a1 1 0 0 0 1.56 0l4-5a1 1 0 1 0-1.56-1.25L13 11.15Z"
                                        clip-rule="evenodd" />
                                    <path fill-rule="evenodd"
                                        d="M9.657 15.874 7.358 13H5a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2h-2.358l-2.3 2.874a3 3 0 0 1-4.685 0ZM17 16a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2H17Z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="sr-only">Descargar</span>
                            </a>
                            <a type="button"
                                href="{{ route('recepcion.estado.reporte', ['socio' => $socio->id, 'tipo' => $radioButon, 'vista' => $vista, 'fInicio' => $fechaInicio, 'fFin' => $fechaFin, 'option' => 's']) }}"
                                target="_blank"
                                class="text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="w-6 h-6">
                                    <path fill-rule="evenodd"
                                        d="M8 3a2 2 0 0 0-2 2v3h12V5a2 2 0 0 0-2-2H8Zm-3 7a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h1v-4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v4h1a2 2 0 0 0 2-2v-5a2 2 0 0 0-2-2H5Zm4 11a1 1 0 0 1-1-1v-4h8v4a1 1 0 0 1-1 1H9Z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="sr-only">Imprimir</span>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div>
            {{ $this->resultSocios->links() }}
        </div>
    </div>
</div>
