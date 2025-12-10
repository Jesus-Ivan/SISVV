@script
    <script>
        $wire.on('ver-ticket', (e) => {
            //Obtener el folio de la venta, a partir de la informacion contenida en el evento
            let folio = e[0].venta;
            // Generar la URL base de la ruta en Blade, usando un marcador temporal (por ejemplo, 'TEMP_FOLIO')
            let ruta = "{{ route('ventas.ticket', ['venta' => 'TEMP_FOLIO']) }}"
            // Reemplazar el marcador temporal con el valor real de 'folio' usando JavaScript
            let ruta_final = ruta.replace('TEMP_FOLIO', folio);
            //Abrir en una pestaña el ticket
            window.open(ruta_final, '_blank');
        });
    </script>
@endscript
