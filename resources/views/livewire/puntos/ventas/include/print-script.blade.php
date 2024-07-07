@script
    <script>
        $wire.on('ver-ticket', (e) => {
            window.open('http://127.0.0.1:8000/venta/ticket/' + e[0].venta, '_blank');
        });
    </script>
@endscript
