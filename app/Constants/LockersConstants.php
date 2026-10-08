<?php

namespace App\Constants;

class LockersConstants
{
    /**
     * Define el enumerable de la columna 'seccion' en la tabla 'locker'
     */
    public const ENUM_SECCION_LOCKER = [
        'DAMAS' => 'DAMAS',
        'CABALLEROS' => 'CABALLEROS',
    ];


    /**
     * Define el enumerable de la columna 'estado_actual' en la tabla 'locker'
     */
    public const ENUM_ESTADO_LOCKER = ['DISPONIBLE', 'OCUPADO', 'EN_MANTENIMIENTO'];

    /**
     * Define los tipos de movimientos que se realizan en los lockers
     */
    public const ENUM_MOVIMIENTOS_LOCKER = [
        'ASIGNACION',
        'BAJA',
        'ASIGNACION_TRANSFERENCIA',
        'BAJA_TRANSFERENCIA',
        'ASIGNACION_MANTENIMIENTO',
        'BAJA_MANTENIMIENTO',
    ];

    /**
     * Define la clave para busqueda de socios
     */
    public const KEY_SOCIO = 'SOCIOS';
    /**
     * Define la clave para busqueda de integrantes de los socios
     */
    public const KEY_INTEGRANTE = 'INTEGRANTES';
}
