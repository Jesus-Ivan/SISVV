<?php

namespace App\Services;

use App\Constants\LockersConstants;
use App\Models\Locker;
use App\Models\CuotaSocio;
use App\Models\HistorialAsignacionLocker;
use App\Models\MovimientoLocker;
use App\Models\SocioCuota;
use Illuminate\Support\Facades\DB;
use Exception;

class LockerService
{
    /**
     * Asigna un locker disponible a un socio, actualiza la cuota y registra el historial.
     */
    public function asignarLocker(
        string $seccion,
        string $numero,
        array $miembroSeleccionado,
        string $usuario,
        ?string $observaciones = null,
        ?int $id_cuota = null
    ): ?Locker {
        return DB::transaction(function () use ($seccion, $numero, $miembroSeleccionado, $usuario, $observaciones, $id_cuota) {
            // Bloqueo de fila para evitar que dos usuarios asignen el mismo locker al mismo tiempo
            $locker = Locker::where([
                ['seccion', '=', $seccion],
                ['numero', '=', $numero]
            ])
                ->lockForUpdate()
                ->firstOrFail();

            if ($locker->estado_actual !== LockersConstants::ENUM_ESTADO_LOCKER[0]) {
                throw new Exception("El locker #{$locker->numero} ({$locker->seccion}) no está disponible.");
            }

            // 1. Cambiar estado actual del locker
            $locker->update([
                'estado_actual' => LockersConstants::ENUM_ESTADO_LOCKER[1],
                'id_socio_actual' => $miembroSeleccionado['id_socio'],
                'id_integrante_actual' => $miembroSeleccionado['id_integrante'],
                'observaciones' => $observaciones,
            ]);

            // 2. Actualizar la cuota mensual activa
            $cuota = SocioCuota::find($id_cuota);
            $cuota->id_locker =  $locker->id_locker;
            $cuota->save();

            // 3. Iniciar registro de ocupación temporal
            HistorialAsignacionLocker::create([
                'id_locker' => $locker->id_locker,
                'id_socio' => $miembroSeleccionado['id_socio'],
                'nombre' => implode(' ', [$miembroSeleccionado['nombre'], $miembroSeleccionado['apellido_p'], $miembroSeleccionado['apellido_m']]),
                'fecha_inicio' => now(),
                'fecha_fin' => null,
            ]);

            // 4. Registrar auditoría de movimiento
            MovimientoLocker::create([
                'id_locker' => $locker->id_locker,
                'id_socio' => $miembroSeleccionado['id_socio'],
                'nombre' => implode(' ', [$miembroSeleccionado['nombre'], $miembroSeleccionado['apellido_p'], $miembroSeleccionado['apellido_m']]),
                'tipo_movimiento' => LockersConstants::ENUM_MOVIMIENTOS_LOCKER[0],
                'fecha_movimiento' => now(),
                'usuario_sistema' => $usuario,
                'observaciones' => $observaciones,
            ]);

            return $locker;
        });
    }


    /**
     * Asigna un locker disponible, sin necesidad de una cuota mensual.
     */
    public function asignarLockerExcep(
        string $id_locker,
        string $usuario,
        ?string $observaciones = null,
    ): ?Locker {
        return DB::transaction(function () use ($id_locker, $usuario, $observaciones) {
            // Bloqueo de fila para evitar que dos usuarios asignen el mismo locker al mismo tiempo
            $locker = Locker::where('id_locker', $id_locker)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locker->estado_actual !== LockersConstants::ENUM_ESTADO_LOCKER[0]) {
                throw new Exception("El locker #{$locker->numero} ({$locker->seccion}) no está disponible.");
            }

            // 1. Cambiar estado actual del locker
            $locker->update([
                'estado_actual' => LockersConstants::ENUM_ESTADO_LOCKER[1],
                'observaciones' => $observaciones,
            ]);

            // 2. Iniciar registro de ocupación temporal
            HistorialAsignacionLocker::create([
                'id_locker' => $locker->id_locker,
                'observaciones' => $locker->observaciones,
                'fecha_inicio' => now(),
                'fecha_fin' => null,
            ]);

            // 3. Registrar auditoría de movimiento
            MovimientoLocker::create([
                'id_locker' => $locker->id_locker,
                'tipo_movimiento' => LockersConstants::ENUM_MOVIMIENTOS_LOCKER[0],
                'fecha_movimiento' => now(),
                'usuario_sistema' => $usuario,
                'observaciones' => $observaciones,
            ]);

            return $locker;
        });
    }

    /**
     * Transfiere la ocupación y cuota de un socio desde un locker origen hacia un locker destino.
     */
    public function transferirLocker(
        int $idLockerOrigen,
        int $idLockerDestino,
        string $usuario,
        ?string $observaciones = null
    ): array {
        return DB::transaction(function () use ($idLockerOrigen, $idLockerDestino, $usuario, $observaciones) {
            $lockerOrigen = Locker::where('id_locker', $idLockerOrigen)->lockForUpdate()->firstOrFail();
            $lockerDestino = Locker::where('id_locker', $idLockerDestino)->lockForUpdate()->firstOrFail();

            if ($lockerOrigen->estado_actual !== LockersConstants::ENUM_ESTADO_LOCKER[1]) {
                throw new Exception("El locker origen #{$lockerOrigen->numero} no está ocupado.");
            }

            if ($lockerDestino->estado_actual !== LockersConstants::ENUM_ESTADO_LOCKER[0]) {
                throw new Exception("El locker destino #{$lockerDestino->numero} no está disponible.");
            }

            $idSocio = $lockerOrigen->id_socio_actual;
            $idIntegrante = $lockerOrigen->id_integrante_actual;

            //Bloqueo y Consulta la cuota activa 
            $cuotaOrigen = SocioCuota::where('id_socio', $idSocio)
                ->where('id_locker', $idLockerOrigen)
                ->lockForUpdate()
                ->first();

            $monto = $cuotaOrigen ? $cuotaOrigen->monto_personalizado : 200.00;

            // ==========================================
            // A. PROCESAR BAJA EN LOCKER ORIGEN
            // ==========================================
            $lockerOrigen->update([
                'estado_actual' => 'DISPONIBLE',
                'id_socio_actual' => null,
                'id_integrante_actual' => null,
                'observaciones' => null
            ]);

            $historialAsignacionOrigen = HistorialAsignacionLocker::where('id_locker', $idLockerOrigen)
                ->where('id_socio', $idSocio)
                ->whereNull('fecha_fin')
                ->first();
            $historialAsignacionOrigen->fecha_fin = now();
            $historialAsignacionOrigen->save();



            MovimientoLocker::create([
                'id_locker' => $idLockerOrigen,
                'id_socio' => $idSocio,
                'nombre' => $historialAsignacionOrigen->nombre,
                'tipo_movimiento' => LockersConstants::ENUM_MOVIMIENTOS_LOCKER[3],
                'fecha_movimiento' => now(),
                'usuario_sistema' => $usuario,
                'observaciones' => $observaciones ?? "Transferencia al locker #$lockerDestino->numero ($lockerDestino->seccion).",
            ]);

            // ==========================================
            // B. PROCESAR ALTA EN LOCKER DESTINO
            // ==========================================
            $lockerDestino->update([
                'estado_actual' => LockersConstants::ENUM_ESTADO_LOCKER[1],
                'id_socio_actual' => $idSocio,
                'id_integrante_actual' => $idIntegrante,
                'observaciones' => $observaciones
            ]);

            HistorialAsignacionLocker::create([
                'id_locker' => $idLockerDestino,
                'id_socio' => $idSocio,
                'nombre' => $historialAsignacionOrigen->nombre,
                'observaciones' => $observaciones,
                'fecha_inicio' => now(),
                'fecha_fin' => null,
            ]);

            if (!is_null($cuotaOrigen)) {
                $cuotaOrigen->update([
                    'id_socio' => $idSocio,
                    'monto_personalizado' => $monto,
                    'id_locker' => $idLockerDestino,
                ]);
            }

            MovimientoLocker::create([
                'id_locker' => $idLockerDestino,
                'id_socio' => $idSocio,
                'nombre' => $historialAsignacionOrigen->nombre,
                'tipo_movimiento' => LockersConstants::ENUM_MOVIMIENTOS_LOCKER[2],
                'fecha_movimiento' => now(),
                'usuario_sistema' => $usuario,
                'observaciones' => $observaciones ?? "Transferencia desde el locker #{$lockerOrigen->numero} ({$lockerOrigen->seccion}).",
            ]);

            return [
                'origen' => $lockerOrigen,
                'destino' => $lockerDestino,
            ];
        });
    }

    /**
     * Desasigna un locker, desactiva la cuota mensual y cierra la línea de tiempo.
     */
    public function bajaLocker(
        int $idLocker,
        string $usuario,
        ?string $observaciones = null
    ): Locker {
        return DB::transaction(function () use ($idLocker, $usuario, $observaciones) {
            $locker = Locker::where('id_locker', $idLocker)->lockForUpdate()->firstOrFail();

            if ($locker->estado_actual !== 'OCUPADO') {
                throw new Exception("El locker #{$locker->numero} no está ocupado.");
            }

            $idSocio = $locker->id_socio_actual;

            // 1. Liberar el locker
            $locker->update([
                'estado_actual' => 'DISPONIBLE',
                'id_socio_actual' => null,
            ]);

            // 2. Cerrar historial de ocupación
            HistorialAsignacionLocker::where('id_locker', $idLocker)
                ->where('id_socio', $idSocio)
                ->whereNull('fecha_fin')
                ->update(['fecha_fin' => now()]);

            // 3. Desactivar cuota recurrente
            CuotaSocio::where('id_socio', $idSocio)
                ->where('id_locker', $idLocker)
                ->where('activo', true)
                ->update([
                    'activo' => false,
                    'fecha_fin' => now(),
                ]);

            // 4. Registrar auditoría de movimiento
            MovimientoLocker::create([
                'id_locker' => $idLocker,
                'id_socio' => $idSocio,
                'tipo_movimiento' => 'BAJA',
                'fecha_movimiento' => now(),
                'usuario_sistema' => $usuario,
                'observaciones' => $observaciones ?? 'Baja de locker efectuada.',
            ]);

            return $locker;
        });
    }

    /**
     * Envía un locker a mantenimiento asegurando que no esté ocupado.
     */
    public function mandarAMantenimiento(
        int $idLocker,
        string $usuario,
        ?string $observaciones = null
    ): Locker {
        return DB::transaction(function () use ($idLocker, $usuario, $observaciones) {
            $locker = Locker::where('id_locker', $idLocker)->lockForUpdate()->firstOrFail();

            if ($locker->estado_actual === 'OCUPADO') {
                throw new Exception("No es posible enviar a mantenimiento un locker que se encuentra ocupado. Procese una baja primero.");
            }

            $locker->update([
                'estado_actual' => 'EN_MANTENIMIENTO',
            ]);

            // Registrar movimiento en bitácora
            MovimientoLocker::create([
                'id_locker' => $idLocker,
                'id_socio' => null,
                'tipo_movimiento' => 'BAJA',
                'fecha_movimiento' => now(),
                'usuario_sistema' => $usuario,
                'observaciones' => $observaciones ?? 'Locker puesto en mantenimiento.',
            ]);

            return $locker;
        });
    }

    /**
     * Retorna un locker en mantenimiento a estado disponible.
     */
    public function salirDeMantenimiento(int $idLocker, string $usuario): Locker
    {
        return DB::transaction(function () use ($idLocker, $usuario) {
            $locker = Locker::where('id_locker', $idLocker)->lockForUpdate()->firstOrFail();

            if ($locker->estado_actual !== 'EN_MANTENIMIENTO') {
                throw new Exception("El locker #{$locker->numero} no está en mantenimiento.");
            }

            $locker->update([
                'estado_actual' => 'DISPONIBLE',
            ]);

            return $locker;
        });
    }
}
