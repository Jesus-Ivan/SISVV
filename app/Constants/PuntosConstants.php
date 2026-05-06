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

    /**
     * Refrencia al 'type' utilizado para el payload del evento 'ComandaDetails'
     */
    public const COMANDA_NUEVA_EVENT ='comanda-nueva';

    /**
     * Refrencia al 'type' utilizado para el payload del evento 'ComandaDetails'
     */
    public const COMANDA_ERROR_EVENT ='comanda-error';

    /**
     * Refrencia al 'type' utilizado para el payload del evento 'ComandaDetails'
     */
    public const COMANDA_ACTUALIZADA_EVENT ='comanda-modificada';

    /**
     * Refrencia al 'type' utilizado para el payload del evento 'ComandaDetails'
     */
    public const COMANDA_REIMP_EVENT ='comanda-reimpresa';
}
