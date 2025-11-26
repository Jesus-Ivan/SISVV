<?php

namespace App\Constants;

class AlmacenConstants
{
    public const CANTIDAD_KEY = "unitario";
    public const PESO_KEY = "peso";
    public const COPAS_KEY = "copas";

    public const BODEGA_INTER_KEY = "INTERNO";
    public const BODEGA_EXTER_KEY = "EXTERNO";

    public const ABARROTES_KEY = "ABA";
    public const MATERIA_KEY = "MP";
    public const SEMIPORDUCIDO_KEY = "SP";
    public const SERVICIO_KEY = "SER";
    public const PLATILLOS_KEY = "PLAT";
    //public const BEBIDAS_KEY = "BEB";

    public const ALIMENTOS_KEY = "ALIMENTOS";
    public const BEBIDAS_KEY = "BEBIDAS";
    public const OTROS_KEY = "OTROS";
    /**
     * Es el valor contenido en la columna 'tipo' de la tabla 'grupos'. 
     * Referente a los grupos disponibles para insumos y presentaciones\
     * \
     * Tambien refiere al valor en la columna 'naturaleza' de la tabla 'bodegas'
     */
    public const INSUMOS_KEY = "INSUM";
    /**
     * Es el valor contenido en la columna 'tipo' de la tabla 'grupos'. 
     * Referente a los grupos disponibles para productos de venta
     */
    public const PRODUCTOS_KEY = "PRODU";

    /**
     *  Refiere al valor en la columna 'naturaleza' de la tabla 'bodegas'
     */
    public const PRESENTACION_KEY = 'PRESEN';

    /**
     * Es la clave del concepto de "E. POR AJUSTE" de la tabla 'conceptos_almacen'
     */
    public const ENT_AJUSTE_KEY = "EPA";

    /**
     * Es la clave del concepto de "S. POR AJUSTE" de la tabla 'conceptos_almacen'
     */
    public const SAL_AJUSTE_KEY = "SPA";

    /**
     * Es la clave del concepto de "E. ALMACEN" de la tabla 'conceptos_almacen'
     */
    public const ENT_KEY = "EDA";

    /**
     * Es la clave del concepto de "S. POR VENTA" de la tabla 'conceptos_almacen'
     */
    public const SAL_VENTA_KEY = "SPV";

    /**
     * Es la clave del concepto de "E. POR TRASPASO ALMACEN" de la tabla 'conceptos_almacen'
     */
    public const ENT_TRASP_KEY = "ETA";

    /**
     * Es la clave del concepto de "S. POR TRASPASO ALMACEN" de la tabla 'conceptos_almacen'
     */
    public const SAL_TRASP_KEY = "STA";


    /**
     * Es la clave del concepto de "E. POR PRODUCCION" de la tabla 'conceptos_almacen'
     */
    public const ENT_PROD_KEY = "EPP";

    /**
     * Es la clave del concepto de "S. POR PRODUCCION" de la tabla 'conceptos_almacen'
     */
    public const SAL_PROD_KEY = "SPP";

    /**
     * Es la clave del concepto de "S. POR MERMA" de la tabla 'conceptos_almacen'
     */
    public const SAL_MER_KEY = "SPM";


    /**
     * Array asociativo que relaciona la clave del punto de venta, con su columna de stock correspondiente de la tabla 'stocks'
     */
    public const PUNTOS_STOCK = [
        'BAR' => 'stock_bar',
        'RES' => 'stock_res',
        'CAD' => 'stock_cad',
        'CAF' => 'stock_caf',
        'LOD' => 'stock_res',
        'LOC' => 'stock_res',
    ];

    /**
     * Array asociativo que contiene la clave-valor de los metodos de pago validos para las entradas de almacen
     */
    public const METODOS_PAGO = [
        0 => 'CREDITO',
        1 => 'CONTADO',
    ];
}
