<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\Caja;
use App\Models\Producto;
use App\Models\ProductoZona;
use App\Models\DetallesVentaProducto;
use App\Models\DetallesVentaPago;
use App\Jobs\ImprimirComandaJob;
use App\Constants\PuntosConstants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class ApiVentaController extends Controller
{
    /**
     * Retorna la lista de ventas del día X para la caja seleccionada.
     */
    public function index(Request $request)
    {
        $request->validate([
            'fecha' => 'nullable|date_format:Y-m-d',
            'corte_caja' => 'nullable|integer',
        ]);

        $fecha = $request->query('fecha', Carbon::now()->toDateString());
        $corteCaja = $request->query('corte_caja');

        $query = Venta::with(['puntoVenta'])
            ->whereDate('fecha_apertura', $fecha);

        if ($corteCaja) {
            $query->where('corte_caja', $corteCaja);
        }

        $ventas = $query->orderBy('fecha_apertura', 'desc')->get();

        return response()->json(
            $ventas->map(function ($v) {
                return [
                    'folio'             => $v->folio,
                    'tipo_venta'        => $v->tipo_venta,
                    'id_socio'          => $v->id_socio,
                    'nombre'            => $v->nombre,
                    'fecha_apertura'    => $v->fecha_apertura,
                    'fecha_cierre'      => $v->fecha_cierre,
                    'total'             => (float) $v->total,
                    'corte_caja'        => $v->corte_caja,
                    'clave_punto_venta' => $v->clave_punto_venta,
                    'num_comensales'    => $v->num_comensales,
                    'estatus'           => is_null($v->fecha_cierre) ? 'Abierta' : 'Cerrada',
                ];
            })
        );
    }

    /**
     * Retorna el detalle completo de una venta (con productos y pagos).
     */
    public function show($folio)
    {
        $venta = Venta::with(['detallesProductos', 'detallesVentasPago.tipoPago'])
            ->where('folio', $folio)
            ->firstOrFail();

        return response()->json([
            'folio'             => $venta->folio,
            'tipo_venta'        => $venta->tipo_venta,
            'id_socio'          => $venta->id_socio,
            'nombre'            => $venta->nombre,
            'fecha_apertura'    => $venta->fecha_apertura,
            'fecha_cierre'      => $venta->fecha_cierre,
            'total'             => (float) $venta->total,
            'corte_caja'        => $venta->corte_caja,
            'clave_punto_venta' => $venta->clave_punto_venta,
            'num_comensales'    => $venta->num_comensales,
            'estatus'           => is_null($venta->fecha_cierre) ? 'Abierta' : 'Cerrada',
            'productos'         => $venta->detallesProductos
                ->groupBy('chunk')
                ->map(function ($items, $chunk) {
                    $parent = $items->first(fn($i) => !str_starts_with($i->nombre, '> ')) ?? $items->first();
                    $mods = $items->filter(fn($i) => str_starts_with($i->nombre, '> '));

                    $subtotalMods = $mods->sum(fn($m) => (float) $m->subtotal);

                    return [
                        'id'             => $parent->id,
                        'clave_producto' => $parent->clave_producto,
                        'nombre'         => $parent->nombre,
                        'precio'         => (float) $parent->precio,
                        'cantidad'       => $parent->cantidad,
                        'chunk'          => (int) $parent->chunk,
                        'observaciones'  => $parent->observaciones ?? '',
                        'subtotal'       => (float) $parent->subtotal + $subtotalMods,
                        'id_estado'      => $parent->id_estado,
                        'modificadores'  => $mods->values()->map(fn($m) => [
                            'clave_producto' => $m->clave_producto,
                            'cantidad'       => $m->cantidad,
                            'precio'         => (float) $m->precio,
                            'nombre'         => ltrim(substr($m->nombre, 2)),
                        ]),
                    ];
                })->values(),
            'pagos'             => $venta->detallesVentasPago->map(fn($p) => [
                'id'            => $p->id,
                'tipo_pago_id'  => $p->id_tipo_pago,
                'nombre_tipo_pago' => $p->tipoPago?->descripcion ?? 'Tipo pago #' . $p->id_tipo_pago,
                'monto'         => (float) $p->monto,
                'fecha'         => $p->created_at ? $p->created_at->toDateTimeString() : now()->toDateTimeString(),
            ]),
        ]);
    }

    /**
     * Registra una nueva comanda (venta abierta) en el sistema.
     */
    public function store(Request $request)
    {
        $request->validate([
            'request_id' => 'required|string|max:50',
            'corte_caja' => 'required|integer',
            'tipo_venta' => 'required|in:socio,invitado,general,empleado',
            'id_socio' => 'required_if:tipo_venta,socio,invitado|nullable|integer',
            'nombre' => 'required_unless:tipo_venta,socio|nullable|string',
            'clave_punto_venta' => 'required|string',
            'productos' => 'required|array|min:1',
            'productos.*.clave_producto' => 'required|integer',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.observaciones' => 'nullable|string',
            'productos.*.modificadores' => 'nullable|array',
            'productos.*.modificadores.*.clave_producto' => 'required|integer',
            'productos.*.modificadores.*.cantidad' => 'required|integer|min:1',
            'productos.*.modificadores.*.precio' => 'nullable|numeric',
            'productos.*.modificadores.*.observaciones' => 'nullable|string',
            'num_comensales' => 'nullable|integer|min:1',
        ]);

        // Verificación de Idempotencia
        $ventaExistente = Venta::where('request_id', $request->request_id)->first();
        if ($ventaExistente) {
            return response()->json(['success' => true, 'folio' => $ventaExistente->folio], 200);
        }

        // Validar que la caja esté abierta
        $caja = Caja::where('corte', $request->corte_caja)
            ->whereNull('fecha_cierre')
            ->first();

        if (!$caja) {
            return response()->json([
                'message' => 'La caja/corte seleccionada no existe o ya está cerrada.'
            ], 422);
        }

        try {
             $folioVenta = null;
            DB::transaction(function () use ($request, $caja, &$folioVenta) {
                // 1. Calcular el total de la venta
                $total = 0;
                $lineasAInsertar = [];

                foreach ($request->productos as $index => $item) {
                    $producto = Producto::findOrFail($item['clave_producto']);
                    $precioBase = (float)$producto->precio_con_impuestos;
                    $cantidadBase = (int)$item['cantidad'];
                    
                    $total += $precioBase * $cantidadBase;

                    // Preparar timestamp único para la agrupación (chunk)
                    $chunk = time() + $index;

                    // Agregar línea del producto principal
                    $lineasAInsertar[] = [
                        'producto' => $producto,
                        'cantidad' => $cantidadBase,
                        'precio' => $precioBase,
                        'subtotal' => $precioBase * $cantidadBase,
                        'observaciones' => $item['observaciones'] ?? '',
                        'chunk' => $chunk,
                        'modif' => false,
                    ];

                    // Procesar modificadores si tiene
                    if (!empty($item['modificadores'])) {
                        foreach ($item['modificadores'] as $modItem) {
                            $modProducto = Producto::findOrFail($modItem['clave_producto']);
                            // Si el request trae un precio override se usa, si no, el del producto modificado
                            $precioMod = isset($modItem['precio']) ? (float)$modItem['precio'] : (float)$modProducto->precio_con_impuestos;
                            $cantidadMod = (int)$modItem['cantidad'];

                            $total += $precioMod * $cantidadMod;

                            $lineasAInsertar[] = [
                                'producto' => $modProducto,
                                'cantidad' => $cantidadMod,
                                'precio' => $precioMod,
                                'subtotal' => $precioMod * $cantidadMod,
                                'observaciones' => $modItem['observaciones'] ?? '',
                                'chunk' => $chunk,
                                'modif' => true,
                            ];
                        }
                    }
                }

                // 2. Resolver el nombre para la cabecera
                $nombre = $request->nombre;
                if ($request->tipo_venta === 'socio') {
                    $socio = \App\Models\Socio::findOrFail($request->id_socio);
                    $nombre = trim("{$socio->nombre} {$socio->apellido_p} {$socio->apellido_m}");
                }

                // 3. Crear cabecera de la venta
                $venta = Venta::create([
                    'tipo_venta' => $request->tipo_venta,
                    'id_socio' => $request->id_socio,
                    'nombre' => $nombre,
                    'fecha_apertura' => now()->format('Y-m-d H:i:s'),
                    'fecha_cierre' => null, // Queda abierta como comanda
                    'total' => $total,
                    'corte_caja' => $caja->corte,
                    'clave_punto_venta' => $request->clave_punto_venta,
                    'request_id' => $request->request_id,
                    'num_comensales' => $request->num_comensales,
                ]);

                $folioVenta = $venta->folio;

                // 4. Crear los detalles de productos
                $inicio = now()->format('Y-m-d H:i:s');
                foreach ($lineasAInsertar as $linea) {
                    $prod = $linea['producto'];

                    // Buscar la zona de impresión si corresponde
                    $zona = null;
                    if ($prod->print_default) {
                        $zona = ProductoZona::where([
                            ['clave_producto', '=', $prod->clave],
                            ['clave_punto', '=', $venta->clave_punto_venta]
                        ])->first();
                    }

                    DetallesVentaProducto::create([
                        'chunk' => $linea['chunk'],
                        'folio_venta' => $folioVenta,
                        'clave_producto' => $prod->clave,
                        'nombre' => $linea['modif'] ? ('> ' . $prod->descripcion) : $prod->descripcion,
                        'cantidad' => $linea['cantidad'],
                        'precio' => $linea['precio'],
                        'observaciones' => $linea['observaciones'],
                        'subtotal' => $linea['subtotal'],
                        'inicio' => $inicio,
                        'tiempo' => null,
                        'id_estado' => $prod->print_default ? PuntosConstants::ID_ESTADO_PRODUCTO_COLA : null,
                        'id_zona' => $zona ? $zona->id_zona : null,
                    ]);
                }

                // Dispatch el trabajo de impresión en cola posterior al commit de la transacción
                ImprimirComandaJob::dispatch($folioVenta)->afterCommit();
            });

            return response()->json([
                'success' => true,
                'message' => 'Venta registrada e impresión enviada a cola.',
                'folio' => $folioVenta,
            ], 201);

        } catch (Exception $e) {
            Log::error('Error en store: ' . $e->getMessage(), [
                'request_id' => $request->request_id ?? 'N/A',
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la comanda: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Agrega productos adicionales a una comanda que sigue abierta.
     */
    public function appendProductos(Request $request, $folio)
    {
        $request->validate([
            'request_id' => 'required|string|max:50', // Validación de ID de operación
            'productos' => 'required|array|min:1',
            'productos.*.clave_producto' => 'required|integer',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.observaciones' => 'nullable|string',
            'productos.*.modificadores' => 'nullable|array',
            'productos.*.modificadores.*.clave_producto' => 'required|integer',
            'productos.*.modificadores.*.cantidad' => 'required|integer|min:1',
            'productos.*.modificadores.*.precio' => 'nullable|numeric',
            'productos.*.modificadores.*.observaciones' => 'nullable|string',
        ]);

        if (\App\Models\VentasSyncLog::where('request_id', $request->request_id)->exists()) {
            return response()->json([
                'success' => true, 
                'message' => 'Operación ya aplicada anteriormente.',
                'folio' => (int)$folio
            ], 200);
        }

        $venta = Venta::where('folio', $folio)
            ->whereNull('fecha_cierre')
            ->first();

        if (!$venta) {
            return response()->json([
                'message' => 'La venta no existe o ya está cerrada.'
            ], 422);
        }

        try {
            DB::transaction(function () use ($request, $venta, $folio) {
                $totalAdicional = 0;
                $lineasAInsertar = [];

                foreach ($request->productos as $index => $item) {
                    $producto = Producto::findOrFail($item['clave_producto']);
                    $precioBase = (float)$producto->precio_con_impuestos;
                    $cantidadBase = (int)$item['cantidad'];
                    
                    $totalAdicional += $precioBase * $cantidadBase;

                    // Preparar timestamp único para la agrupación (chunk)
                    $chunk = time() + $index;

                    // Agregar línea del producto principal
                    $lineasAInsertar[] = [
                        'producto' => $producto,
                        'cantidad' => $cantidadBase,
                        'precio' => $precioBase,
                        'subtotal' => $precioBase * $cantidadBase,
                        'observaciones' => $item['observaciones'] ?? '',
                        'chunk' => $chunk,
                        'modif' => false,
                    ];

                    // Procesar modificadores si tiene
                    if (!empty($item['modificadores'])) {
                        foreach ($item['modificadores'] as $modItem) {
                            $modProducto = Producto::findOrFail($modItem['clave_producto']);
                            $precioMod = isset($modItem['precio']) ? (float)$modItem['precio'] : (float)$modProducto->precio_con_impuestos;
                            $cantidadMod = (int)$modItem['cantidad'];

                            $totalAdicional += $precioMod * $cantidadMod;

                            $lineasAInsertar[] = [
                                'producto' => $modProducto,
                                'cantidad' => $cantidadMod,
                                'precio' => $precioMod,
                                'subtotal' => $precioMod * $cantidadMod,
                                'observaciones' => $modItem['observaciones'] ?? '',
                                'chunk' => $chunk,
                                'modif' => true,
                            ];
                        }
                    }
                }

                // 1. Insertar las nuevas líneas en detalles
                $inicio = now()->format('Y-m-d H:i:s');
                foreach ($lineasAInsertar as $linea) {
                    $prod = $linea['producto'];

                    // Buscar la zona de impresión si corresponde
                    $zona = null;
                    if ($prod->print_default) {
                        $zona = ProductoZona::where([
                            ['clave_producto', '=', $prod->clave],
                            ['clave_punto', '=', $venta->clave_punto_venta]
                        ])->first();
                    }

                    DetallesVentaProducto::create([
                        'chunk' => $linea['chunk'],
                        'folio_venta' => $venta->folio,
                        'clave_producto' => $prod->clave,
                        'nombre' => $linea['modif'] ? ('> ' . $prod->descripcion) : $prod->descripcion,
                        'cantidad' => $linea['cantidad'],
                        'precio' => $linea['precio'],
                        'observaciones' => $linea['observaciones'],
                        'subtotal' => $linea['subtotal'],
                        'inicio' => $inicio,
                        'tiempo' => null,
                        'id_estado' => $prod->print_default ? PuntosConstants::ID_ESTADO_PRODUCTO_COLA : null,
                        'id_zona' => $zona ? $zona->id_zona : null,
                    ]);
                }

                // 2. Actualizar total de la venta cabecera
                $venta->total = (float)$venta->total + $totalAdicional;
                $venta->save();

                // 2. REGISTRO DE IDEMPOTENCIA
                \App\Models\VentasSyncLog::create([
                    'request_id' => $request->request_id,
                    'folio_venta' => $folio
                ]);

                // Dispatch el trabajo de impresión en cola posterior al commit de la transacción
                ImprimirComandaJob::dispatch($venta->folio)->afterCommit();
            });

            return response()->json([
                'success' => true,
                'message' => 'Productos adicionales agregados a la comanda e impresión enviada a cola.',
                'folio' => $venta->folio,
            ], 200);

        } catch (Exception $e) {
            Log::error('Error en appendProductos: ' . $e->getMessage(), [
                'request_id' => $request->request_id ?? 'N/A',
                'folio' => $folio,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar productos a la comanda: ' . $e->getMessage()
            ], 500);
        }
    }


    public function transferirProducto(Request $request, $folio)
    {
        $request->validate([
            'folio_destino' => 'required|integer|exists:ventas,folio',
            'chunk'         => 'required|integer',
        ]);

        $ventaOrigen = Venta::where('folio', $folio)->whereNull('fecha_cierre')->firstOrFail();
        $ventaDestino = Venta::where('folio', $request->folio_destino)
            ->whereNull('fecha_cierre')
            ->where('corte_caja', $ventaOrigen->corte_caja)
            ->firstOrFail();

        DB::transaction(function () use ($request, $ventaOrigen, $ventaDestino) {
            $filas = DetallesVentaProducto::where('folio_venta', $ventaOrigen->folio)
                ->where('chunk', $request->chunk)
                ->get();

            if ($filas->isEmpty()) {
                throw new Exception('No se encontraron productos con ese chunk en la venta origen');
            }

            $subtotalTransferido = $filas->sum('subtotal');

            foreach ($filas as $fila) {
                $fila->update(['folio_venta' => $ventaDestino->folio]);
            }

            $ventaOrigen->total  = (float)$ventaOrigen->total - $subtotalTransferido;
            $ventaDestino->total = (float)$ventaDestino->total + $subtotalTransferido;

            $ventaOrigen->save();
            $ventaDestino->save();
        });

        return response()->json([
            'success' => true,
            'message' => 'Producto transferido correctamente',
        ]);
    }

    /**
     * Reintenta la impresión de productos en estado COLA o ERROR.
     */
    public function reimprimir($folio)
    {
        $venta = Venta::where('folio', $folio)->first();

        if (!$venta) {
            return response()->json(['message' => 'Venta no encontrada.'], 404);
        }

        $pendientes = DetallesVentaProducto::where('folio_venta', $folio)
            ->whereIn('id_estado', [
                PuntosConstants::ID_ESTADO_PRODUCTO_COLA,
                PuntosConstants::ID_ESTADO_PRODUCTO_ERROR,
            ])
            ->get();

        if ($pendientes->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No hay productos pendientes por imprimir.',
            ]);
        }

        // Resetear solo ERROR -> COLA (los que ya están en COLA se quedan)
        foreach ($pendientes as $p) {
            if ($p->id_estado === PuntosConstants::ID_ESTADO_PRODUCTO_ERROR) {
                $p->id_estado = PuntosConstants::ID_ESTADO_PRODUCTO_COLA;
                $p->save();
            }
        }

        try {
            ImprimirComandaJob::dispatch($folio);
        } catch (\Exception $e) {
            \Log::error("Error al despachar reimpresión del folio {$folio}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al encolar la reimpresión.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Reimpresión encolada para ' . $pendientes->count() . ' producto(s).',
        ]);
    }
}