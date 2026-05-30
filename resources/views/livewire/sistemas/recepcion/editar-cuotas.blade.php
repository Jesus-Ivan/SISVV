<div class="ms-3 mx-3">
    {{-- INFO DEL SOCIO --}}
    <div class="mb-4">
        <p class="text-sm text-gray-500 dark:text-gray-400">No. Socio: {{ $socio->id }}</p>
        <h4 class="text-xl font-bold text-gray-900 dark:text-white uppercase">
            {{ $socio->nombre . ' ' . $socio->apellido_p . ' ' . $socio->apellido_m }}
        </h4>
        <div class="flex items-center gap-2 flex-wrap mt-1">
            <span class="text-sm text-gray-500 dark:text-gray-400">Membresías:</span>
            @forelse($socio->cuotasMembresia as $sc)
                <span class="px-2 py-0.5 text-xs font-medium rounded bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                    {{ $sc->cuota->clave_membresia }} — {{ $sc->cuota->tipo }}
                </span>
            @empty
                <span class="text-sm text-gray-400 italic">Sin membresías registradas</span>
            @endforelse
        </div>
    </div>

    {{-- DESCRIPCIÓN --}}
    <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">
        Aquí puedes asignar un precio personalizado a cada cuota del socio. Si el campo se deja vacío, se aplicará el precio base del catálogo. Usa el botón <span class="font-medium text-gray-700 dark:text-gray-300">Limpiar</span> para eliminar un precio personalizado ya guardado.
    </p>

    @if (session('success'))
        <div class="flex items-center p-4 mb-4 text-green-800 border-t-4 border-green-300 bg-green-50 dark:text-green-400 dark:bg-gray-800 dark:border-green-800">
            <svg class="flex-shrink-0 w-4 h-4 me-3" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if (session('fail'))
        <div class="flex items-center p-4 mb-4 text-red-800 border-t-4 border-red-300 bg-red-50 dark:text-red-400 dark:bg-gray-800 dark:border-red-800">
            <svg class="flex-shrink-0 w-4 h-4 me-3" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <span class="text-sm font-medium">{{ session('fail') }}</span>
        </div>
    @endif

    {{-- TABLA DE CUOTAS --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-6 py-3">Concepto</th>
                    <th class="px-6 py-3">Tipo</th>
                    <th class="px-6 py-3 text-right">Precio base</th>
                    <th class="px-6 py-3 text-center w-56">Precio personalizado</th>
                    <th class="px-6 py-3 text-right">Precio efectivo</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($cuotas as $index => $cuota)
                    <tr wire:key="cuota-{{ $cuota['id'] }}"
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-3 font-medium text-gray-900 dark:text-white">
                            {{ $cuota['descripcion'] }}
                        </td>
                        <td class="px-6 py-3">{{ $cuota['tipo'] }}</td>
                        <td class="px-6 py-3 text-right">${{ number_format($cuota['monto_base'], 2) }}</td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-1">
                                <span class="text-gray-500">$</span>
                                <input type="number" min="0" step="0.01"
                                    wire:model="cuotas.{{ $index }}.monto_personalizado"
                                    placeholder="{{ number_format($cuota['monto_base'], 2) }}"
                                    class="w-full text-sm text-right border border-gray-300 rounded-lg p-2 bg-gray-50 text-gray-900 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white
                                        {{ !is_null($cuota['monto_personalizado']) ? 'border-purple-400 bg-purple-50 dark:bg-purple-900/20' : '' }}" />
                            </div>
                            @error("cuotas.{$index}.monto_personalizado")
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </td>
                        <td class="px-6 py-3 text-right font-semibold text-gray-900 dark:text-white">
                            ${{ number_format($cuota['monto_personalizado'] ?? $cuota['monto_base'], 2) }}
                        </td>
                        <td class="px-6 py-3 text-center">
                            @if (!is_null($cuota['monto_personalizado']))
                                <button type="button" wire:click="limpiar({{ $index }})"
                                    title="Quitar precio personalizado"
                                    class="text-xs text-gray-500 border border-gray-400 hover:bg-gray-100 hover:text-gray-700 font-medium rounded-lg px-2 py-1 dark:border-gray-500 dark:text-gray-400 dark:hover:bg-gray-700">
                                    Limpiar
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                            Este socio no tiene cuotas asignadas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- BOTONES --}}
    <div class="flex gap-3 mt-4">
        <button type="button" wire:click="guardar" wire:loading.attr="disabled"
            class="inline-flex items-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
            <svg wire:loading.remove wire:target="guardar" class="w-5 h-5 me-2" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z" clip-rule="evenodd"/>
            </svg>
            <div wire:loading wire:target="guardar" class="me-2">
                @include('livewire.utils.loading', ['w' => 5, 'h' => 5])
            </div>
            Guardar cambios
        </button>
        <a href="{{ route('sistemas.lista-socios') }}"
            class="inline-flex items-center text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">
            <svg class="w-5 h-5 me-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12l4-4m-4 4 4 4"/>
            </svg>
            Regresar
        </a>
    </div>
</div>
