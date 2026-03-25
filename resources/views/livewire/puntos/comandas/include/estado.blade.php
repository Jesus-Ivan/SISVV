<p>
    @switch($prod->id_estado)
        @case($estado_en_cola)
            <span
                class="inline-flex items-center bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-gray-900 dark:text-gray-300">
                {{ $prod->EstadoProductoVenta->descripcion }}
            </span>
        @break

        @case($estado_impreso)
            <span
                class="inline-flex items-center bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300">
                {{ $prod->EstadoProductoVenta->descripcion }}
            </span>
        @break

        @case($estado_listo)
            <span
                class="inline-flex items-center bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">
                {{ $prod->EstadoProductoVenta->descripcion }}
            </span>
        @break

        @case($estado_error)
            <span
                class="inline-flex items-center bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-red-900 dark:text-red-300">
                {{ $prod->EstadoProductoVenta->descripcion }}
            </span>
        @break

        @case($estado_cancelado)
            <span>{{ $prod->EstadoProductoVenta->descripcion }}</span>
        @break

        @default
            <span>{{ $prod->EstadoProductoVenta->descripcion }}</span>
    @endswitch
</p>
