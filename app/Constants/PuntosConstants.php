<?php

namespace App\Constants;

class PuntosConstants
{
    /**
     * Representa el ingreso a la caja, proveniente de una venta ordinaria
     */
    public const INGRESO_KEY = "in";
    /**
     * Representa el ingreso a la caja, proveniente de una venta pendiente
     */
    public const INGRESO_PENDIENTE_KEY = "in-pe";

    /**
     * Referencia a la tabla 'estado_productos_ventas'\
     * Representa el estado (en cola de impresion) de un producto, de una venta concreta.
     */
    public const ID_ESTADO_PRODUCTO_COLA = '0';

    /**
     * Referencia a la tabla 'estado_productos_ventas'\
     * Representa el estado (impreso) de un producto, de una venta concreta.
     */
    public const ID_ESTADO_PRODUCTO_IMPRESO = '1';

    /**
     * Referencia a la tabla 'estado_productos_ventas'\
     * Representa el estado (listo en cocina) de un producto, de una venta concreta.
     */
    public const ID_ESTADO_PRODUCTO_LISTO = '2';

    /**
     * Referencia a la tabla 'estado_productos_ventas'\
     * Representa el estado (error de impresion) de un producto, de una venta concreta.
     */
    public const ID_ESTADO_PRODUCTO_ERROR = '3';

    /**
     * Referencia a la tabla 'estado_productos_ventas'\
     * Representa el estado (producto cancelado) de un producto, de una venta concreta.
     */
    public const ID_ESTADO_PRODUCTO_CANCELADO = '4';
}
