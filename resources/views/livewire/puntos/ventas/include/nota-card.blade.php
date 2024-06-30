<a href="{{ route('pv.ventas.editar', ['folioventa' => $item->folio, 'codigopv' => $codigopv]) }}"
    class="max-w-sm p-4 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
    {{-- HEAD --}}
    <div>
        <h4 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">
            {{ $item->nombre }}
        </h4>
        <h3 class="font-bold tracking-tight text-gray-900 dark:text-white">
            <div class="grid col-span-2 grid-cols-2">
                <p>Venta: {{ $item->folio }}</p>
                <p>Socio: {{ $item->id_socio ? $item->id_socio : '' }}</p>
            </div>
        </h3>
    </div>
    {{-- BODY --}}
    <div>
        <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">
            {{ $item->fecha_apertura }}
        </p>

        {{-- <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400">
            <li>
                At least 10 characters
            </li>
            <li>
                At least one lowercase character
            </li>
            <li>
                Inclusion of at least one special charact
            </li>
        </ul> --}}
        <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">
            Subtotal: ${{ $item->total }}
        </p>
    </div>
</a>
