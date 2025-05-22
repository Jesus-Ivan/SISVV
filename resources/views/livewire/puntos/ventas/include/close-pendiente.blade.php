@script
    <script>
        $wire.on('cerrar-pagina', (event) => {
            // Pequeña demora opcional para asegurar que cualquier actualización de UI se complete
            setTimeout(function() {
                window.close();
            }, 300); // 300 milisegundos de demora
        });
    </script>
@endscript
