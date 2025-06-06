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
    public const BEBIDAS_KEY = "BEB";

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
     * Es el valor contenido en la columna 'tipo' de la tabla 'grupos'. 
     * Referente a los grupos disponibles para insumos
     */
    public const GRUPO_INSUMO_KEY = "INSUM";
    /**
     * Es el valor contenido en la columna 'tipo' de la tabla 'grupos'. 
     * Referente a los grupos disponibles para productos de venta
     */
    public const GRUPO_PRODUC_KEY = "PRODU";

    /**
     * Array asociativo que contiene la clave-valor de los metodos de pago validos para las entradas de almacen
     */
    public const METODOS_PAGO = [
        0 => 'CREDITO',
        1 => 'CONTADO',
    ];
}
