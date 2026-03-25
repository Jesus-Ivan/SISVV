<style>
    html {
        margin: 10pt;
        font-size: 12pt;
    }

    p {
        margin-top: 2pt;
        margin-bottom: 2pt;
    }

    .mayus {
        text-transform: uppercase;
    }

    .puntos {
        max-width: 40pt;
        text-align: left;
        overflow: hidden;
        /* Oculta el contenido que no cabe */
        white-space: nowrap;
        /* Evita que el texto se envuelva en varias líneas */
        text-overflow: ellipsis;
        /* Muestra "..." al final del texto */
    }

    h1,
    h2,
    h3,
    h4 {
        text-align: center;
        margin-top: 3pt;
        margin-bottom: 12pt;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        justify-content: initial;
        padding: 1pt;
    }

    th,
    td {
        line-height: 8pt;
        padding: 1pt;
        text-align: left;
    }
</style>

<head>
    <title>{{ $venta->folio }}</title>
</head>
{{-- Punto de venta --}}
<h3 style="margin-top: 50px;">{{ $venta->puntoVenta->nombre }}</h3>
{{-- Datos de venta --}}
<p>ACCION: {{ $venta->id_socio }}</p>
<p>{{ $venta->nombre }}</p>
<p>VENTA: {{ $venta->folio }}</p>
<hr>
{{-- Datos de productos --}}
@foreach ($productos as $key => $prod)
    <p>{{ $prod->cantidad }} {{ $prod->nombre }}</p>
    <p> - {{ $prod->observaciones }}</p>
    @if ($key < count($productos) - 1)
        @if ($prod->chunk != $productos[$key + 1]->chunk)
            <hr>
        @endif
    @endif
@endforeach
<br>
<p style="text-align: center">{{ $f_inicio->format('d-m-Y H:i') }}</p>
