<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Caja;
use App\Models\Socio;
use App\Models\Producto;
use App\Models\Grupos;
use App\Models\GruposModificadores;
use App\Models\TipoPago;
use Illuminate\Http\Request;

class ApiSyncController extends Controller
{
    /**
     * Retorna las cajas (cortes de caja) activas asociadas al usuario autenticado.
     */
    public function getCajasActivas(Request $request)
    {
        $user = $request->user();
        $cajas = Caja::with('puntoVenta')
            ->whereNull('fecha_cierre')
            ->where('clave_punto_venta', '!=', 'REC')
            ->get()
            ->map(function ($caja) {
                return [
                    'id' => $caja->corte,
                    'nombre' => optional($caja->puntoVenta)->nombre ?? 'Sin punto de venta',
                    'fecha_apertura' => $caja->fecha_apertura,
                    'fecha_cierre' => $caja->fecha_cierre,
                    'activo' => is_null($caja->fecha_cierre),
                    'mesero_id' => $caja->id_usuario,
                    'corte' => $caja->corte,
                    'cambio_inicial' => (float) $caja->cambio_inicial,
                    'clave_punto_venta' => $caja->clave_punto_venta,
                ];
            });
            return response()->json($cajas);
    }

    /**
     * Retorna el catálogo de tipos de pago activos.
     */
    public function getTiposPago(Request $request)
    {
        $tipos = TipoPago::orderBy('descripcion')
            ->get()
            ->map(function ($tipo) {
                return [
                    'id' => $tipo->id,
                    'nombre' => $tipo->descripcion,
                    'requiere_socio' => false,
                    'requiere_firma' => false,
                    'activo' => true,
                ];
            });

        return response()->json($tipos);
    }

    /**
     * Retorna el catálogo completo de socios no cancelados, con su membresía y sus integrantes.
     * Esto permite su almacenamiento local para búsquedas Offline.
     */
    public function syncSocios(Request $request)
    {
        // Se excluyen los socios cancelados o sin membresía asociada para optimizar almacenamiento local
        $socios = Socio::with(['socioMembresia', 'socioMembresias.membresia', 'integrantesSocio'])
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($socio) {
                return [
                    'id' => $socio->id,
                    'nombre' => $socio->nombre,
                    'apellido_p' => $socio->apellido_p,
                    'apellido_m' => $socio->apellido_m,
                    'num_accion' => $socio->num_accion ?? null,
                    'firma' => (bool)$socio->firma,
                    'img_path' => $socio->img_path ? asset($socio->img_path) : null,
                    'membresia' => $socio->socioMembresia ? [
                        'clave' => $socio->socioMembresia->clave_membresia,
                        'estado' => $socio->socioMembresia->estado,
                    ] : null,
                    'membresias' => $socio->socioMembresias->map(function ($sm) {
                        return [
                            'clave' => $sm->clave_membresia,
                            'estado' => $sm->estado,
                            'descripcion' => optional($sm->membresia)->descripcion,
                            'consumo_minimo' => $sm->membresia ? (float)$sm->membresia->consumo_minimo : null,
                        ];
                    }),
                    'integrantes' => $socio->integrantesSocio->map(function ($integrante) {
                        return [
                            'id' => $integrante->id,
                            'nombre' => $integrante->nombre_integrante,
                            'apellido_p' => $integrante->apellido_p_integrante,
                            'apellido_m' => $integrante->apellido_m_integrante,
                            'parentesco' => $integrante->parentesco,
                            'img_path' => $integrante->img_path_integrante ? asset($integrante->img_path_integrante) : null,
                        ];
                    }),
                ];
            });

        return response()->json($socios);
    }

    /**
     * Retorna el catálogo de productos disponibles para la venta con sus modificadores.
     */
    public function syncProductos(Request $request)
    {
        // Traemos todos los productos activos de venta
        $productos = Producto::with(['grupo', 'subgrupo', 'grupoModif.grupoModif', 'modificador'])
            ->whereNot('estado', 0) // Productos activos
            ->get()
            ->map(function ($producto) {
                return [
                    'clave' => $producto->clave,
                    'descripcion' => $producto->descripcion,
                    'costo_unitario' => (float)$producto->costo_unitario,
                    'precio' => (float)$producto->precio_con_impuestos,
                    'print_default' => (bool)$producto->print_default,
                    'id_grupo' => $producto->id_grupo,
                    'grupo' => $producto->grupo ? $producto->grupo->descripcion : 'N/A',
                    'id_subgrupo' => $producto->id_subgrupo,
                    'subgrupo' => $producto->subgrupo ? $producto->subgrupo->descripcion : 'N/A',
                    // Grupos de modificadores que aplican a este producto
                    'grupos_modificadores' => $producto->grupoModif->map(function ($gm) {
                        return [
                            'id_grupo' => $gm->id_grupo,
                            'descripcion' => $gm->grupoModif ? $gm->grupoModif->descripcion : 'N/A',
                            'modif_incluidos' => $gm->modif_incluidos,
                            'modif_maximos' => $gm->modif_maximos,
                            'forzar_captura' => (bool)$gm->forzar_captura,
                        ];
                    }),
                    // Opciones de modificador disponibles para este producto
                    'modificadores_opciones' => $producto->modificador->map(function ($mod) {
                        // El modificador apunta a otro producto (ej. cebolla extra)
                        $productoModif = $mod->productoModif;
                        return [
                            'id' => $mod->id,
                            'id_grupo' => $mod->id_grupo,
                            'clave_modificador' => $mod->clave_modificador,
                            'descripcion' => $productoModif ? $productoModif->descripcion : 'N/A',
                            'precio_override' => $mod->precio !== null ? (float)$mod->precio : ($productoModif ? (float)$productoModif->precio_con_impuestos : 0.0),
                            'print_default' => $productoModif ? (bool)$productoModif->print_default : false,
                        ];
                    }),
                ];
            });

        return response()->json($productos);
    }

    /**
     * Retorna los tipos de venta válidos.
     */
    public function getTiposVenta(Request $request)
    {
        return response()->json(['socio', 'invitado', 'general', 'empleado']);
    }
}