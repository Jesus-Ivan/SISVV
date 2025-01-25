<div>
    @foreach ($nominas as $nomina)
        @if ($nomina->diferencia_efectivo + $nomina->extras - $nomina->descuento > 0)
            @include('reportes.Nominas.nomina')
        @endif
    @endforeach
</div>
