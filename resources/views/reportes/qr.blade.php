<style>
    html {
        font-size: x-small
    }

    h1,
    h2 {
        text-align: center
    }

    table {
        width: 100%
    }

    thead {
        border-top: 1px solid black;
        border-bottom: 1px solid black;
    }

    th {
        text-align: left;
        padding-top: 2px;
        padding-bottom: 2px;
    }

    p {
        margin: 0;
        text-align: center;
    }

    .remarcardo {
        font-style: normal,
            font-weight: bold,
    }
</style>

<div>
    <?php
    $path = 'storage/image001.jpg';
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    ?>

    <div>
        <table>
            <p>
                <img src="<?php echo $base64; ?>" height="40" alt="logoVistaVerde" />
            </p>
            <br>
            <p>
                <img src="data:image/svg+xml;base64,{{ base64_encode($valor) }}">
            </p>
            <h2> {{ $resultSocio->id }} </h2>
            <p class="remarcardo" style="font-size: 14px;">
                {{ $resultSocio->nombre . ' ' . $resultSocio->apellido_p . ' ' . $resultSocio->apellido_m }}
            </p>
        </table>
    </div>
</div>
