<?php

namespace App\Livewire\Forms;

use App\Constants\AlmacenConstants;
use App\Constants\PuntosConstants;
use App\Events\ComandaDetails;
use App\Models\Caja;
use App\Models\Copa;
use App\Models\CorreccionVenta;

use App\Models\DetallesCaja;
use App\Models\DetallesVentaPago;
use App\Models\DetallesVentaProducto;
use App\Models\EstadoCuenta;
use App\Models\Modificador;
use App\Models\ModifProducto;
use App\Models\Producto;
use App\Models\MotivoCorreccion;
use App\Models\ProductoZona;
use App\Models\PuntoVenta;
use App\Models\Socio;
use App\Models\SocioMembresia;
use App\Models\Stock;
use App\Models\TipoPago;
use App\Models\Venta;
use App\Models\ZonaImpresion;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Locked;
use Livewire\Form;

use function Livewire\Volt\with;

class VentaForm extends Form
{
    public $tipo_venta = "socio";   //El tipo de venta a realizar
    public $invitado;               //Invitado
    public $nombre_invitado;        //El nombre del invitado
    public $nombre_p_general;       //Nombre del cliente cuanso selecciona publico general
    public $nombre_empleado;        //Nombre del empleado
    public $socioSeleccionado;      //El socio seleccionado
    public $socio = [];             //Datos del socio
    public $no_comensal = null;     //La cantidad de comensales en la mesa
    public $para_llevar = false;    //Indica si la venta es para llevar

    public $socioPago;              //El socio seleccionado para agregar en metodo de pago
    public $id_pago;                //id del tipo de pago seleccionado en el modal
    public $monto_pago;             //el monto a pagar
    public $propina;                //Si dejo o no propina

    public $seachProduct = '';        //Input de busqueda de productos
    public $selected = [];            //Almacena los codigos de productos seleccionados del modal.

    public $productosTable = [];      //array de productos, que se muestran en la tabla (productos agregados)

    public $pagosTable = [];          //Array de pagos que se muestran en la tabla
    #[Locked]
    public $totalVenta = 0;           //El costo total de los articulos

    #[Locked]
    public $totalPago = 0;            //El total de los pagos
    public $totalPropina = 0;         //El total de la propina
    //public $descuento = 0;          //En caso de que se aplique un descuento a la cuenta
    //public $totalSinDescuento = 0;  //Centa total temporal en caso que se le aplique un descuento
    //public $totalConDescuento = 0;  //El total de la venta final

    #[Locked]
    public $indexSeleccionado;        //Es el posible producto a transferir o eliminar
    public $seachVenta = '';          //el parametro de busqueda, del modal de tranferir producto
    public $operaciones_transferencia = []; //Detalle estructurado de transferencias de productos
    public $lista_eliminados = [];    //La lista de los productos a eliminar

    public $permisospv;               //Almacena los permisos del usuario en el punto


    //REGLAS PARA VENTA AL SOCIO
    public $socioVentaRules = [
        'socio' => 'min:1',
        'productosTable' => 'min:1',
        'pagosTable' => 'min:1',
    ];

    //REGLAS PARA VENTA AL INVITADO
    public $invitadoVentaRules = [
        'socio' => 'min:1',
        'nombre_invitado' => 'required',
        'productosTable' => 'min:1',
        'pagosTable' => 'min:1',
    ];

    //REGLAS PARA VENTA AL PUBLICO GENERAL
    public $generalVentaRules = [
        'nombre_p_general' => 'required',
        'productosTable' => 'min:1',
        'pagosTable' => 'min:1',
    ];

    //REGLAS PARA VENTA AL EMPLEADO
    public $empleadoVentaRules = [
        'nombre_empleado' => 'required',
        'productosTable' => 'min:1',
        'pagosTable' => 'min:1'
    ];

    //RESETEAR VALORES DEL SELECT Y TABLA DE PAGOS
    public function resetVentas()
    {
        $this->reset(['socio', 'nombre_invitado', 'nombre_p_general', 'nombre_empleado']);
        $this->reset(['pagosTable', 'id_pago', 'monto_pago', 'socioPago', 'propina']);
    }

    /* Agrega los articulos seleccionados a la tabla.
        La funcion recibe el array de todos los items mostrado en el modal.
    */
    public function agregarItems($productosResult)
    {
        //Filtramos los productos seleccionados, cuyo valor sea true del checkBox
        $total_seleccionados = array_filter($this->selected, function ($val) {
            return $val;
        });

        //Si no se han seleccionado articulos, impedir ejecuccion
        if (!count($total_seleccionados) > 0) {
            return false;
        }
        //Recorrer todo el array de seleccionados
        foreach ($total_seleccionados as $key => $value) {
            //Se busca el registro del producto en base a su codigo.
            $producto = $productosResult->find($key);
            //Se anexa el producto al array de la tabla
            $this->productosTable[] = [
                'codigo_catalogo' => $producto->codigo,
                'nombre' => $producto->nombre,
                'cantidad' => 1,
                'precio' => $producto->costo_unitario,
                'subtotal' => $producto->costo_unitario,
                'observaciones' => '',
                'tiempo' => '1'
            ];
        }

        //Limpiamos las propiedades
        $this->selected = [];    //Productos seleccionados
        $this->recalcularSubtotales();
    }

    /**
     * Elimina los productos agregados con el mismo timeStamp\
     * y los guarda en una lista temporal de eliminaciones suaves
     */
    public function eliminarArticuloBD($timeStamp, $id_eliminacion, $motivo)
    {
        //Filtrar los elementos con el mismo timeStamp, de la tabla de productos
        $new_array = array_filter($this->productosTable, function ($item) use ($timeStamp) {
            return $item['chunk'] != $timeStamp;
        });
        //Filtrar aquellos elementos que se eliminaran
        $deleted = array_filter($this->productosTable, function ($item) use ($timeStamp) {
            return $item['chunk'] == $timeStamp;
        });
        //Agregar la informacion adicional a los elementos eliminados
        $deleted = array_map(function ($producto) use ($id_eliminacion, $motivo) {
            $producto['id_cancelacion'] = $id_eliminacion;
            $producto['motivo_cancelacion'] = $motivo;
            return $producto;
        }, $deleted);
        //Agregarlos a la lista de eliminados
        $this->lista_eliminados = array_merge($this->lista_eliminados, $deleted);

        //Re asignar variable filtrada (con los elementos restantes)
        $this->productosTable = $new_array;
        //Limpiar la propiedad
        $this->reset('indexSeleccionado');
    }

    /**
     * Retira los elementos del arreglo de la tabla. sin guardarlos en la lista de eliminaciones suaves
     */
    public function eliminarArticulo($timeStamp)
    {
        //Filtrar los elementos con el mismo timeStamp, de la tabla de productos
        $this->productosTable = array_filter($this->productosTable, function ($item) use ($timeStamp) {
            return $item['chunk'] != $timeStamp;
        });
        //Limpiar la propiedad (por si habia quedado el registro previo)
        $this->reset('indexSeleccionado');
    }

    public function calcularSubtotal($productoIndex, $eValue)
    {
        //Se actualiza la cantidad del producto en la tabla
        $this->productosTable[$productoIndex]['cantidad'] = $eValue;
        //Se calcula el subtotal del producto
        $this->productosTable[$productoIndex]['subtotal'] = $this->productosTable[$productoIndex]['precio'] * $eValue;
        $this->actualizarTotal();
    }

    /**
     * Actualiza la sumatoria del subtotal de la tabla de productos\
     * (Obtiene el total de la cuenta y actualiza la propiedad correspondiente)
     */
    public function actualizarTotal()
    {
        $productos = array_filter($this->productosTable, function ($prod) {
            return !array_key_exists('moved', $prod);
        });
        //Se actualiza el total de los productos
        $this->totalVenta = array_sum(array_column($productos, 'subtotal'));
    }

    //Recibe una instancia del modelo 'Socio' con el registro de la BD, para el registro del pago
    public function setSocioPago($socio)
    {
        //Validamos si el socio tiene al menos una membresía activa
        $tieneActiva = SocioMembresia::where('id_socio', $socio->id)->whereNot('estado', 'CAN')->exists();
        $tieneAlguna = SocioMembresia::where('id_socio', $socio->id)->exists();
        if (!$tieneAlguna) {
            throw new Exception('No se encontro membresia registrada', 2);
        } else if (!$tieneActiva) {
            throw new Exception('Membresia de socio cancelada', 2);
        }
        $this->socioPago = $socio;
    }

    public function agregarPago()
    {
        //Validamos las entradas
        $reglas = [
            'id_pago' => 'required',
            'monto_pago' => 'required|numeric',
        ];

        if ($this->tipo_venta == 'socio' || $this->tipo_venta == 'invitado') {
            $reglas['socioPago'] = 'required';          // Agregar validacion de número de socio si es venta para socio
        }

        $validated = $this->validate($reglas);          //Validamos los atributos

        $this->validarFirma($validated);                                       //Validamos la firma
        $id_socio = $this->socioPago ? $this->socioPago->id : null; //Si es venta para socio, se obtiene el id del socio

        switch ($this->tipo_venta) {
            case 'socio':
                $nombre = $this->socioPago->nombre . ' ' . $this->socioPago->apellido_p . ' ' . $this->socioPago->apellido_m;
                break;
            case 'invitado':
                $nombre = $this->nombre_invitado;
                break;
            case 'empleado':
                $nombre = $this->nombre_empleado;
                break;
            case 'general':
                $nombre = $this->nombre_p_general;
                break;
            default:
                break;
        }

        //Se agrega el pago a la tabla de pagos
        $this->pagosTable[] = [
            'id_socio' => $id_socio,
            'nombre' => $nombre,
            'id_tipo_pago' => $this->id_pago,
            'descripcion_tipo_pago' => TipoPago::find($this->id_pago)->descripcion,
            'monto_pago' => $validated['monto_pago'],
            'propina' => $this->propina,
        ];
        //si es una venta diferente de invitado, limpiamos el socioPago
        if ($this->tipo_venta != 'invitado') {
            //Limpiar todos los campos del modal del pago
            $this->reset('socioPago');
        }
        //Continuamos limpiando el resto de propiedades del modal de pago
        $this->reset('id_pago', 'monto_pago', 'propina');

        $this->actualizarPago();
    }

    public function eliminarPago($pagoIndex)
    {
        unset($this->pagosTable[$pagoIndex]);
        $this->actualizarPago();
    }

    public function actualizarPago()
    {
        //Se actualiza el total del pago
        $this->totalPago = array_sum(array_column($this->pagosTable, 'monto_pago'));

        //Aplicar descuento si es que existe
        /* if ($this->descuento > 0) {
            $descuentoAplicado = $totalSinDescuento * ($this->descuento / 100);
            $this->totalPago = $totalSinDescuento - $descuentoAplicado;
        } else {
            $this->totalPago = $totalSinDescuento;
        }*/
    }

    //Se ejecuta para cerrar una venta completa, si esta es nueva
    public function cerrarVentaNueva($codigopv)
    {
        //Recalculamos cada subtotal de la tabla. (por si hubo un fallo de red en alguna peticion hacia el metodo 'calcularSubtotal')
        $this->recalcularSubtotales();
        //Validamos la informacion de la venta
        switch ($this->tipo_venta) {
            case 'socio':
                $venta = $this->validate($this->socioVentaRules);
                break;
            case 'invitado':
                $venta = $this->validate($this->invitadoVentaRules);
                break;
            case 'general':
                $venta = $this->validate($this->generalVentaRules);
                break;
            case 'empleado':
                $venta = $this->validate($this->empleadoVentaRules);
                break;
            default:
                break;
        }
        //Agregar el numero de comensales
        $venta['no_comensal'] = $this->no_comensal;

        //Verificamos si el total de pago, es el mismo que el total de los productos
        $this->verificarMontos();

        //Variable auxiliar para almacenar el folio resultado de la venta
        $folioVenta = 0;
        //Se crea la transaccion
        DB::transaction(function () use ($venta, $codigopv, &$folioVenta) {
            //Crear la venta y guardamos el resultado de la insersion en la BD, en la variable 'resultVenta'
            $resultVenta = $this->registrarVenta($venta, $codigopv, true, $this->tipo_venta);
            //Obtenemos el folio
            $folioVenta = $resultVenta->folio;

            //crear los detalles de los productos
            $this->registrarProductosVenta($folioVenta, $venta);
            //Descontar stocks
            //$this->verificarStock($codigopv, $venta['productosTable']);

            //Crear los detalles de los pagos
            $this->registrarPagosVenta($folioVenta, $venta, $codigopv);
            //Crear el detalle de caja
            $this->crearMovimientoCaja(
                $resultVenta,
                $venta['pagosTable'],
                PuntosConstants::INGRESO_KEY
            );
        }, 2);
        //Limpiamos atributos
        $this->limpiarComponente();
        //Devolvemos objeto del resultado al componente
        return $folioVenta;
    }

    //Se ejecuta para guardar una venta, cuando es nueva
    public function guardarVentaNueva($codigopv)
    {
        //Recalculamos cada subtotal de la tabla. (por si hubo un fallo de red en alguna peticion hacia el metodo 'calcularSubtotal')
        $this->recalcularSubtotales();
        //Validamos la informacion de la venta
        switch ($this->tipo_venta) {
            case 'socio':
                $venta = $this->validate([
                    'socio' => 'min:1',
                ]);
                break;
            case 'invitado':
                $venta = $this->validate([
                    'socio' => 'min:1',
                    'nombre_invitado' => 'required',
                ]);
                break;
            case 'general':
                $venta = $this->validate([
                    'nombre_p_general' => 'required',
                ]);
                break;
            case 'empleado':
                $venta = $this->validate([
                    'nombre_empleado' => 'required',
                ]);
                break;
            default:
                break;
        }
        //Agregamos los productos al resultado de la validacion
        $venta['productosTable'] = $this->productosTable;
        $venta['no_comensal'] = $this->no_comensal;
        //Duplicamos variable para pasarla a la funcion anonima de la transaccion
        $tipo_venta = $this->tipo_venta;

        //Variable auxiliar para almacenar el folio resultado de la venta
        $folioVenta = 0;
        //Se crea la transaccion
        DB::transaction(function () use ($venta, $codigopv, $tipo_venta, &$folioVenta) {
            //Crear la venta y guardamos el resultado de la insersion en la BD.
            $resultVenta = $this->registrarVenta($venta, $codigopv, false, tipo_venta: $tipo_venta);
            //Obtenemos el folio
            $folioVenta = $resultVenta->folio;
            //crear los detalles de los productos
            $this->registrarProductosVenta($resultVenta->folio, $venta);
        }, 2);
        $this->limpiarComponente();
        //Devolvemos objeto del resultado al componente
        return $folioVenta;
    }

    /**
     * Actualizar una venta existente (actualiza la tabla de productos y total de la venta)
     */
    public function guardarVentaExistente($folio)
    {
        //Verificamos si la venta no esta cerrada
        $this->validarVentaCerrada($folio);
        //Recalculamos cada subtotal de la tabla. (por si hubo un fallo de red en alguna peticion hacia el metodo 'calcularSubtotal')
        $this->recalcularSubtotales();
        //Creamos una fecha de inicio para los detalles de los productos que se van a guardar
        $inicio = now()->format('Y-m-d H:i:s');

        DB::transaction(function () use ($folio, $inicio) {
            //Revisar si hubo algun movimiento de mercancia en la BD.
            $this->verificarProductos($folio);
            //Obtener la venta principal
            $result = Venta::find($folio);
            //Mapa de tiempos por chunk (los modificadores heredan el tiempo del producto base)
            $tiemposPorChunk = collect($this->productosTable)
                ->filter(fn($p) => !array_key_exists('modif', $p) && array_key_exists('chunk', $p))
                ->pluck('tiempo', 'chunk');
            //Recorremos todos los items de la tabla
            foreach ($this->productosTable as $key => $producto) {
                // Si este item pertenece a un chunk que está siendo transferido/dividido, moverProductos() lo procesará
                if ($this->chunkEstaEnTransferencia($producto['chunk'] ?? null)) {
                    continue;
                }
                //Verificamos si el item que se itera, cuenta con un 'id' de la base de datos
                if (array_key_exists('id', $producto)) {
                    //Actualizar el registro en la BD
                    DetallesVentaProducto::where('id', $producto['id'])
                        ->update(
                            [
                                'cantidad' => $producto['cantidad'],
                                'subtotal' => $producto['subtotal'],
                                'observaciones' => $producto['observaciones'],
                            ]
                        );
                } else {
                    $zona = $this->obtenerZona($producto, $result);
                    $tiempo = array_key_exists('modif', $producto) && array_key_exists('chunk', $producto)
                        ? ($tiemposPorChunk[$producto['chunk']] ?? null)
                        : ($producto['tiempo'] ?? null);
                    //Crear el nuevo item
                    DetallesVentaProducto::create([
                        'chunk' => $producto['chunk'],
                        'folio_venta' => $folio,
                        'clave_producto' => $producto['clave_producto'],
                        'nombre' => $producto['nombre'],
                        'cantidad' => $producto['cantidad'],
                        'precio' => $producto['precio'],
                        'observaciones' => $producto['observaciones'],
                        'subtotal' => $producto['subtotal'],
                        'inicio' => $inicio,
                        'tiempo' => $tiempo,
                        'id_estado' => $producto['print_default'] ? PuntosConstants::ID_ESTADO_PRODUCTO_COLA : null,
                        'id_zona' => $zona?->id_zona,
                        'mesero' => auth()->user()?->name
                    ]);
                }
            }
            // 2. Movemos los productos de venta (transferencias y divisiones)
            $this->moverProductos($folio);

            // 3. Eliminamos suavemente los productos cancelados por el usuario
            $this->eliminarProductos();
            $this->crearCorreccion($folio);

            // 4. Actualizamos el total de la venta de origen calculando la suma real de productos vigentes en la BD
            $totalRealOrigen = DetallesVentaProducto::where('folio_venta', $folio)->sum('subtotal') ?? 0;
            Venta::where('folio', $folio)->update(['total' => $totalRealOrigen]);
            $this->totalVenta = $totalRealOrigen;
        }, 2);


        //Verificamos si hay productos modificados
        if (count($this->lista_eliminados) || count($this->operaciones_transferencia)) {
            $zonasIds = $this->obtenerZonasModificadas();
            $zonas = ZonaImpresion::whereIn('id', $zonasIds)->get();
            $ventaModel = Venta::find($folio);

            foreach ($zonas as $zona) {
                if (!$zona) continue;
                //Avisamos en tiempo real (la modificacion de la venta origen)
                broadcast(new ComandaDetails(
                    PuntosConstants::COMANDA_ACTUALIZADA_EVENT,
                    $ventaModel,
                    $zona
                ));
            }

            // Avisamos en tiempo real a las ventas destino involucradas
            $destinosNotificar = [];
            foreach ($this->operaciones_transferencia as $op) {
                if ($op['es_fast_path']) {
                    $destinosNotificar[] = $op['folio_destino_unico'];
                } else {
                    foreach ($op['ventas_destino'] as $d) {
                        $destinosNotificar[] = $d['folio'];
                    }
                }
            }
            foreach (array_unique(array_filter($destinosNotificar)) as $fDest) {
                $vDest = Venta::find($fDest);
                if ($vDest) {
                    foreach ($zonas as $zona) {
                        broadcast(new ComandaDetails(
                            PuntosConstants::COMANDA_ACTUALIZADA_EVENT,
                            $vDest,
                            $zona
                        ));
                    }
                }
            }
        }
    }

    //Cerrar una venta existente (actualiza toda la venta)
    public function cerrarVentaExistente($folio, $codigopv)
    {
        //Recalculamos cada subtotal de la tabla. (por si hubo un fallo de red en alguna peticion hacia el metodo 'calcularSubtotal')
        $this->recalcularSubtotales();
        //Verificamos si la venta no esta cerrada
        $this->validarVentaCerrada($folio);
        //Validamos que de la venta tenga metodos de pago y productos
        $venta = $this->validate([
            'productosTable' => 'min:1',
            'pagosTable' => 'min:1'
        ]);
        //Verificamos si el total de pago, es el mismo que el total de los productos
        $this->verificarMontos();

        DB::transaction(function () use ($folio, $venta, $codigopv) {
            //Verificamos si la lista de productos no fue alterada
            $this->verificarProductos($folio);
            //Guardamos los metodos de pago
            $this->registrarPagosVenta($folio, $venta, $codigopv);
            //Guardamos los cambios de la tabla de productos
            $this->guardarVentaExistente($folio);
            //Buscamos la venta original, para saber el corte al que pertenece
            $resultVenta = Venta::find($folio);
            //Crear el detalle de caja
            $this->crearMovimientoCaja(
                $resultVenta,
                $venta['pagosTable'],
                PuntosConstants::INGRESO_KEY
            );
            //Cerramos la venta con la fecha actual
            Venta::where('folio', $folio)->update(['fecha_cierre' => now()->format('Y-m-d H:i:s')]);
        }, 2);
    }

    //Guarda el inidice del producto a transferir
    public function saveProductoTransferible($index)
    {
        //agregar el indice
        $this->indexSeleccionado = $index;
    }

    /**
     * Comprueba si un chunk está siendo transferido
     */
    public function chunkEstaEnTransferencia($chunk): bool
    {
        if (!$chunk) return false;
        foreach ($this->operaciones_transferencia as $op) {
            if ($op['chunk_original'] == $chunk) {
                return true;
            }
        }
        return false;
    }

    /**
     * Obtiene los IDs de zonas de impresión afectadas por eliminaciones y transferencias
     */
    public function obtenerZonasModificadas(): array
    {
        $zonas = [];
        foreach ($this->lista_eliminados as $el) {
            if (!empty($el['id_zona'])) $zonas[] = $el['id_zona'];
        }
        foreach ($this->operaciones_transferencia as $op) {
            if (!empty($op['producto_base']['id_zona'])) {
                $zonas[] = $op['producto_base']['id_zona'];
            }
            foreach ($op['modificadores_originales'] as $mo) {
                if (!empty($mo['id_zona'])) $zonas[] = $mo['id_zona'];
            }
        }
        return array_values(array_unique(array_filter($zonas)));
    }

    /**
     * Recalcula precios de modificadores aplicando las reglas de grupo_modificador_producto
     */
    public function recalcularPreciosModificadores($clave_producto_base, int $cantidad_unidades, array $modificadores_asignados): array
    {
        if (empty($modificadores_asignados) || $cantidad_unidades <= 0) {
            return [];
        }

        $gruposReglas = ModifProducto::where('clave_producto', $clave_producto_base)->get()->keyBy('id_grupo');
        $modificadoresCatalogo = Modificador::where('clave_producto', $clave_producto_base)->get()->keyBy('clave_modificador');

        $porGrupo = [];
        foreach ($modificadores_asignados as $mod) {
            $claveMod = $mod['clave_producto'];
            $cat = $modificadoresCatalogo->get($claveMod);
            $idGrupo = $cat ? $cat->id_grupo : ($mod['id_grupo'] ?? 0);
            $precioCatalogo = $cat && !is_null($cat->precio) ? (float)$cat->precio : (float)($mod['precio'] ?? 0);

            $porGrupo[$idGrupo][] = [
                'clave_producto' => $claveMod,
                'nombre' => $mod['nombre'],
                'cantidad' => (int)$mod['cantidad'],
                'precio_catalogo' => $precioCatalogo,
                'id_zona' => $mod['id_zona'] ?? null,
                'print_default' => $mod['print_default'] ?? false,
            ];
        }

        $resultado = [];

        foreach ($porGrupo as $idGrupo => $items) {
            $regla = $gruposReglas->get($idGrupo);
            $incluidosTotal = $regla ? ($regla->modif_incluidos * $cantidad_unidades) : 0;
            $incluidosConsumidos = 0;

            foreach ($items as $item) {
                $cant = $item['cantidad'];
                if ($cant <= 0) continue;

                $gratuitos = max(0, min($cant, $incluidosTotal - $incluidosConsumidos));
                $conCosto = $cant - $gratuitos;
                $incluidosConsumidos += $gratuitos;

                if ($gratuitos > 0) {
                    $resultado[] = [
                        'clave_producto' => $item['clave_producto'],
                        'nombre' => $item['nombre'],
                        'cantidad' => $gratuitos,
                        'precio' => 0.00,
                        'subtotal' => 0.00,
                        'modif' => true,
                        'id_zona' => $item['id_zona'],
                        'print_default' => $item['print_default'],
                    ];
                }

                if ($conCosto > 0) {
                    $resultado[] = [
                        'clave_producto' => $item['clave_producto'],
                        'nombre' => $item['nombre'],
                        'cantidad' => $conCosto,
                        'precio' => $item['precio_catalogo'],
                        'subtotal' => round($conCosto * $item['precio_catalogo'], 2),
                        'modif' => true,
                        'id_zona' => $item['id_zona'],
                        'print_default' => $item['print_default'],
                    ];
                }
            }
        }

        return $resultado;
    }

    /**
     * Ejecuta la transferencia/división en memoria Livewire
     */
    public function ejecutarTransferenciaEnMemoria(array $payload)
    {
        if (is_null($this->indexSeleccionado) || !isset($this->productosTable[$this->indexSeleccionado])) {
            throw new Exception("No hay ningún producto seleccionado para transferir.");
        }

        $productoCapturado = $this->productosTable[$this->indexSeleccionado];
        $chunk = $productoCapturado['chunk'];

        $chunkItems = array_values(array_filter($this->productosTable, function ($p) use ($chunk) {
            return ($p['chunk'] ?? null) == $chunk;
        }));

        $modificadoresOriginales = array_values(array_filter($chunkItems, function ($p) use ($productoCapturado) {
            return ($p['clave_producto'] != $productoCapturado['clave_producto']) || (array_key_exists('modif', $p) && $p['modif']);
        }));

        // Caso Fast-Path (100% a una sola venta destino)
        if (!empty($payload['es_fast_path'])) {
            $folioDestino = (int)$payload['folio_destino'];

            $op = [
                'chunk_original' => $chunk,
                'producto_base' => $productoCapturado,
                'modificadores_originales' => $modificadoresOriginales,
                'es_fast_path' => true,
                'folio_destino_unico' => $folioDestino,
                'ventas_destino' => [
                    [
                        'folio' => $folioDestino,
                        'cantidad' => $productoCapturado['cantidad'],
                        'subtotal_base' => $productoCapturado['subtotal'],
                        'modificadores' => $modificadoresOriginales,
                    ]
                ],
                'remanente_origen' => [
                    'cantidad' => 0,
                    'subtotal_base' => 0,
                    'modificadores' => [],
                ]
            ];

            $this->operaciones_transferencia[] = $op;

            // Retirar de la tabla en memoria
            $this->productosTable = array_values(array_filter($this->productosTable, function ($p) use ($chunk) {
                return ($p['chunk'] ?? null) != $chunk;
            }));

            $this->reset('indexSeleccionado');
            $this->actualizarTotal();
            return;
        }

        // Caso División / Múltiples destinos
        $ventasDestinoPayload = $payload['ventas_destino'] ?? [];
        $remanenteOrigenPayload = $payload['remanente_origen'] ?? ['cantidad' => 0, 'modificadores' => []];

        $ventasDestinoProcesadas = [];
        foreach ($ventasDestinoPayload as $vDest) {
            $cantDest = (int)($vDest['cantidad_destino'] ?? $vDest['cantidad'] ?? 0);
            if ($cantDest <= 0) continue;

            $modsAsignados = $vDest['modificadores'] ?? [];
            $modsRecalculados = $this->recalcularPreciosModificadores(
                $productoCapturado['clave_producto'],
                $cantDest,
                $modsAsignados
            );

            $subtotalBase = round($cantDest * $productoCapturado['precio'], 2);

            $ventasDestinoProcesadas[] = [
                'folio' => (int)$vDest['folio'],
                'cantidad' => $cantDest,
                'subtotal_base' => $subtotalBase,
                'modificadores' => $modsRecalculados,
            ];
        }

        $cantRemanente = (int)($remanenteOrigenPayload['cantidad'] ?? 0);
        $modsRemanenteRecalculados = [];
        $subtotalRemanenteBase = 0;

        if ($cantRemanente > 0) {
            $subtotalRemanenteBase = round($cantRemanente * $productoCapturado['precio'], 2);
            $modsRemanenteRecalculados = $this->recalcularPreciosModificadores(
                $productoCapturado['clave_producto'],
                $cantRemanente,
                $remanenteOrigenPayload['modificadores'] ?? []
            );
        }

        $op = [
            'chunk_original' => $chunk,
            'producto_base' => $productoCapturado,
            'modificadores_originales' => $modificadoresOriginales,
            'es_fast_path' => false,
            'folio_destino_unico' => null,
            'ventas_destino' => $ventasDestinoProcesadas,
            'remanente_origen' => [
                'cantidad' => $cantRemanente,
                'subtotal_base' => $subtotalRemanenteBase,
                'modificadores' => $modsRemanenteRecalculados,
            ]
        ];

        $this->operaciones_transferencia[] = $op;

        // Actualizar productosTable en memoria
        if ($cantRemanente === 0) {
            $this->productosTable = array_values(array_filter($this->productosTable, function ($p) use ($chunk) {
                return ($p['chunk'] ?? null) != $chunk;
            }));
        } else {
            $nuevosProductos = [];
            foreach ($this->productosTable as $p) {
                if (($p['chunk'] ?? null) == $chunk) {
                    if ($p['clave_producto'] == $productoCapturado['clave_producto'] && empty($p['modif'])) {
                        $p['cantidad'] = $cantRemanente;
                        $p['subtotal'] = $subtotalRemanenteBase;
                        $nuevosProductos[] = $p;

                        foreach ($modsRemanenteRecalculados as $modRem) {
                            $modRem['chunk'] = $chunk;
                            $modRem['observaciones'] = '';
                            $modRem['tiempo'] = null;
                            $nuevosProductos[] = $modRem;
                        }
                    }
                } else {
                    $nuevosProductos[] = $p;
                }
            }
            $this->productosTable = $nuevosProductos;
        }

        $this->reset('indexSeleccionado');
        $this->actualizarTotal();
    }

    /**
     * Alias para compatibilidad
     */
    public function agregarATransferir($payload)
    {
        $this->ejecutarTransferenciaEnMemoria($payload);
    }

    /**
     * Consulta la BD la lista de productos de la venta, y si existio un movimiento de productos entre ventas o cambio de cantidad, lanza una excepcion
     */
    private function verificarProductos($folio)
    {
        // Buscar los productos activos que tiene la venta en la BD con bloqueo para consistencia transaccional
        $productos_bd = DetallesVentaProducto::where('folio_venta', $folio)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        // 1. Verificar productos actuales en la tabla (que tienen id)
        foreach ($this->productosTable as $producto) {
            if (array_key_exists('id', $producto) && $producto['id']) {
                $prodBd = $productos_bd->get($producto['id']);
                $prodNombre = $producto['nombre'] ?? 'seleccionado';

                if (!$prodBd) {
                    throw new Exception("El producto '{$prodNombre}' fue eliminado o transferido por otro usuario. Actualice la página.");
                }

                // Si este item pertenece a una transferencia procesada en esta misma petición,
                // su validación de cantidad original se realiza en el bloque 3 (operaciones_transferencia).
                if (!$this->chunkEstaEnTransferencia($producto['chunk'] ?? null)) {
                    if ((int)$prodBd->cantidad !== (int)$producto['cantidad']) {
                        throw new Exception("El producto '{$prodNombre}' fue modificado por otro usuario (la cantidad en la venta cambió de {$producto['cantidad']} a {$prodBd->cantidad}). Actualice la página.");
                    }
                }
            }
        }

        // 2. Verificar productos a eliminar
        foreach ($this->lista_eliminados as $producto) {
            if (array_key_exists('id', $producto) && $producto['id']) {
                $prodBd = $productos_bd->get($producto['id']);
                $prodNombre = $producto['nombre'] ?? 'seleccionado';

                if (!$prodBd) {
                    throw new Exception("El producto '{$prodNombre}' a eliminar ya fue eliminado o transferido por otro usuario. Actualice la página.");
                }

                // Si NO pertenece a un chunk transferido en esta misma sesión, su cantidad en BD debe coincidir con la esperada
                if (!$this->chunkEstaEnTransferencia($producto['chunk'] ?? null)) {
                    if ((int)$prodBd->cantidad !== (int)$producto['cantidad']) {
                        throw new Exception("El producto '{$prodNombre}' a eliminar fue modificado por otro usuario (la cantidad en la venta cambió de {$producto['cantidad']} a {$prodBd->cantidad}). Actualice la página.");
                    }
                }
            }
        }

        // 3. Verificar productos en transferencias
        foreach ($this->operaciones_transferencia as $op) {
            $baseId = $op['producto_base']['id'] ?? null;
            $prodNombre = $op['producto_base']['nombre'] ?? 'seleccionado';
            if ($baseId) {
                $prodBd = $productos_bd->get($baseId);
                if (!$prodBd) {
                    throw new Exception("El producto '{$prodNombre}' a transferir fue eliminado o transferido por otro usuario. Actualice la página.");
                }
                if ((int)$prodBd->cantidad !== (int)$op['producto_base']['cantidad']) {
                    throw new Exception("El producto '{$prodNombre}' a transferir fue modificado por otro usuario (la cantidad en la venta cambió de {$op['producto_base']['cantidad']} a {$prodBd->cantidad}). Actualice la página.");
                }
            }
            foreach ($op['modificadores_originales'] as $modOrig) {
                if (array_key_exists('id', $modOrig) && $modOrig['id']) {
                    $modBd = $productos_bd->get($modOrig['id']);
                    $modNombre = $modOrig['nombre'] ?? 'modificador';
                    if (!$modBd) {
                        throw new Exception("El modificador '{$modNombre}' del producto a transferir fue eliminado o transferido por otro usuario. Actualice la página.");
                    }
                    if ((int)$modBd->cantidad !== (int)$modOrig['cantidad']) {
                        throw new Exception("El modificador '{$modNombre}' del producto a transferir fue modificado por otro usuario (la cantidad en la venta cambió de {$modOrig['cantidad']} a {$modBd->cantidad}). Actualice la página.");
                    }
                }
            }
        }

        // 4. Verificar que todas las ventas destino involucradas sigan abiertas
        foreach ($this->operaciones_transferencia as $op) {
            $prodNombre = $op['producto_base']['nombre'] ?? 'seleccionado';
            $destinos = $op['es_fast_path']
                ? [$op['folio_destino_unico']]
                : array_column($op['ventas_destino'], 'folio');

            foreach (array_unique(array_filter($destinos)) as $folioDest) {
                $vDest = Venta::where('folio', $folioDest)->whereNull('fecha_cierre')->first();
                if (!$vDest) {
                    throw new Exception("El producto {$prodNombre} no se pudo transferir a la venta #{$folioDest}. Ya está cerrada o fue fusionada. Actualice la página.");
                }
            }
        }
    }

    /**
     * Mueve los productos correspondientes y actualiza el total de las ventas destino y origen
     */
    private function moverProductos($folio)
    {
        $timestampOperacion = time();
        $chunkOffset = 0;

        // 1. Procesar operaciones de transferencia detalladas
        foreach ($this->operaciones_transferencia as $op) {
            $chunkOrig = $op['chunk_original'];

            // Caso Fast-Path (100% a una sola venta destino)
            if ($op['es_fast_path']) {
                $folioDest = $op['folio_destino_unico'];
                $prodNombre = $op['producto_base']['nombre'] ?? 'seleccionado';
                $ventaDest = Venta::where('folio', $folioDest)->whereNull('fecha_cierre')->first();
                if (!$ventaDest) {
                    throw new Exception("El producto {$prodNombre} no se pudo transferir a la venta #{$folioDest}. Ya está cerrada o fue fusionada. Actualice la página.");
                }

                $subtotalChunk = DetallesVentaProducto::where('chunk', $chunkOrig)
                    ->where('folio_venta', $folio)
                    ->sum('subtotal');

                DetallesVentaProducto::where('chunk', $chunkOrig)
                    ->where('folio_venta', $folio)
                    ->update(['folio_venta' => $folioDest]);

                $ventaDest->total = DetallesVentaProducto::where('folio_venta', $folioDest)->sum('subtotal') ?? 0;
                $ventaDest->save();
                continue;
            }

            // Caso División / Múltiples destinos
            $inicio = now()->format('Y-m-d H:i:s');
            $prodBase = $op['producto_base'];
            $modsOrigMap = collect($op['modificadores_originales'])->keyBy('clave_producto');

            foreach ($op['ventas_destino'] as $idxDest => $dest) {
                $folioDest = $dest['folio'];
                $prodNombre = $prodBase['nombre'] ?? 'seleccionado';
                $ventaDest = Venta::where('folio', $folioDest)->whereNull('fecha_cierre')->first();
                if (!$ventaDest) {
                    throw new Exception("El producto {$prodNombre} no se pudo transferir a la venta #{$folioDest}. Ya está cerrada o fue fusionada. Actualice la página.");
                }

                $chunkNuevo = (string) ($timestampOperacion + $chunkOffset++);
                $subtotalTotalDestino = $dest['subtotal_base'];

                DetallesVentaProducto::create([
                    'chunk' => $chunkNuevo,
                    'folio_venta' => $folioDest,
                    'clave_producto' => $prodBase['clave_producto'],
                    'nombre' => $prodBase['nombre'],
                    'cantidad' => $dest['cantidad'],
                    'precio' => $prodBase['precio'],
                    'subtotal' => $dest['subtotal_base'],
                    'observaciones' => $prodBase['observaciones'] ?? '',
                    'inicio' => $prodBase['inicio'] ?? $inicio,
                    'tiempo' => $prodBase['tiempo'] ?? null,
                    'id_estado' => $prodBase['id_estado'] ?? null,
                    'id_zona' => $prodBase['id_zona'] ?? null,
                    'mesero' => auth()->user()?->name
                ]);

                foreach ($dest['modificadores'] as $mod) {
                    $subtotalTotalDestino += $mod['subtotal'];
                    $modOrig = $modsOrigMap->get($mod['clave_producto']);
                    $modEstado = $modOrig ? ($modOrig['id_estado'] ?? null) : ($prodBase['id_estado'] ?? null);
                    $modZona = $modOrig ? ($modOrig['id_zona'] ?? null) : ($mod['id_zona'] ?? $prodBase['id_zona'] ?? null);

                    DetallesVentaProducto::create([
                        'chunk' => $chunkNuevo,
                        'folio_venta' => $folioDest,
                        'clave_producto' => $mod['clave_producto'],
                        'nombre' => $mod['nombre'],
                        'cantidad' => $mod['cantidad'],
                        'precio' => $mod['precio'],
                        'subtotal' => $mod['subtotal'],
                        'observaciones' => $modOrig['observaciones'] ?? '',
                        'inicio' => $prodBase['inicio'] ?? $inicio,
                        'tiempo' => null,
                        'id_estado' => $modEstado,
                        'id_zona' => $modZona,
                        'mesero' => auth()->user()?->name
                    ]);
                }

                $ventaDest->total = DetallesVentaProducto::where('folio_venta', $folioDest)->sum('subtotal') ?? 0;
                $ventaDest->save();
            }

            // Manejo de la venta de origen
            $rem = $op['remanente_origen'];
            $remanenteEliminado = collect($this->lista_eliminados)->contains('chunk', $chunkOrig);

            if ($rem['cantidad'] === 0) {
                // No quedó nada en origen: eliminar definitivamente con forceDelete para no ensuciar reportes
                DetallesVentaProducto::where('chunk', $chunkOrig)
                    ->where('folio_venta', $folio)
                    ->forceDelete();
            } elseif ($remanenteEliminado) {
                $itemEliminado = collect($this->lista_eliminados)->firstWhere('chunk', $chunkOrig);
                $idCancelacion = $itemEliminado['id_cancelacion'] ?? null;
                $motivoCancelacion = $itemEliminado['motivo_cancelacion'] ?? null;
                $usuarioCancela = auth()->user()?->name ?? ($itemEliminado['usuario_cancela'] ?? null);
                $fechaCancela = now()->format('Y-m-d H:i:s');

                // El usuario transfirió una parte y posteriormente canceló el remanente en la tabla
                DetallesVentaProducto::withTrashed()
                    ->where('id', $prodBase['id'])
                    ->update([
                        'cantidad' => $rem['cantidad'],
                        'subtotal' => $rem['subtotal_base'],
                        'id_cancelacion' => $idCancelacion,
                        'motivo_cancelacion' => $motivoCancelacion,
                        'deleted_at' => $fechaCancela,
                        'usuario_cancela' => $usuarioCancela,
                    ]);

                // Eliminar los modificadores viejos en origen
                DetallesVentaProducto::where('chunk', $chunkOrig)
                    ->where('id', '<>', $prodBase['id'])
                    ->where('folio_venta', $folio)
                    ->forceDelete();

                // Crear los modificadores del producto remanente eliminado para fines de auditoría,
                // directamente con la misma información de cancelación del producto base
                foreach ($rem['modificadores'] as $modRem) {
                    $modOrig = $modsOrigMap->get($modRem['clave_producto']);
                    $modEstado = $modOrig ? ($modOrig['id_estado'] ?? null) : ($prodBase['id_estado'] ?? null);
                    $modZona = $modOrig ? ($modOrig['id_zona'] ?? null) : ($modRem['id_zona'] ?? $prodBase['id_zona'] ?? null);

                    DetallesVentaProducto::create([
                        'chunk' => $chunkOrig,
                        'folio_venta' => $folio,
                        'clave_producto' => $modRem['clave_producto'],
                        'nombre' => $modRem['nombre'],
                        'cantidad' => $modRem['cantidad'],
                        'precio' => $modRem['precio'],
                        'subtotal' => $modRem['subtotal'],
                        'observaciones' => $modOrig['observaciones'] ?? '',
                        'inicio' => $prodBase['inicio'] ?? $inicio,
                        'tiempo' => null,
                        'id_estado' => $modEstado,
                        'id_zona' => $modZona,
                        'mesero' => auth()->user()?->name,
                        'id_cancelacion' => $idCancelacion,
                        'motivo_cancelacion' => $motivoCancelacion,
                        'deleted_at' => $fechaCancela,
                        'usuario_cancela' => $usuarioCancela,
                    ]);
                }
            } else {
                // Actualizar producto base en origen
                DetallesVentaProducto::where('id', $prodBase['id'])
                    ->update([
                        'cantidad' => $rem['cantidad'],
                        'subtotal' => $rem['subtotal_base'],
                    ]);

                // Eliminar modificadores viejos en origen
                DetallesVentaProducto::where('chunk', $chunkOrig)
                    ->where('id', '<>', $prodBase['id'])
                    ->where('folio_venta', $folio)
                    ->forceDelete();

                // Insertar los modificadores restantes recalculados en origen
                foreach ($rem['modificadores'] as $modRem) {
                    $modOrig = $modsOrigMap->get($modRem['clave_producto']);
                    $modEstado = $modOrig ? ($modOrig['id_estado'] ?? null) : ($prodBase['id_estado'] ?? null);
                    $modZona = $modOrig ? ($modOrig['id_zona'] ?? null) : ($modRem['id_zona'] ?? $prodBase['id_zona'] ?? null);

                    DetallesVentaProducto::create([
                        'chunk' => $chunkOrig,
                        'folio_venta' => $folio,
                        'clave_producto' => $modRem['clave_producto'],
                        'nombre' => $modRem['nombre'],
                        'cantidad' => $modRem['cantidad'],
                        'precio' => $modRem['precio'],
                        'subtotal' => $modRem['subtotal'],
                        'observaciones' => $modOrig['observaciones'] ?? '',
                        'inicio' => $prodBase['inicio'] ?? $inicio,
                        'tiempo' => null,
                        'id_estado' => $modEstado,
                        'id_zona' => $modZona,
                        'mesero' => auth()->user()?->name
                    ]);
                }
            }
        }
    }

    /**
     * Realiza la eliminacion SUAVE de los productos
     */
    public function eliminarProductos()
    {
        //Usuario actual
        $user = auth()->user();
        //Recorremos todos los items a eliminar
        foreach ($this->lista_eliminados as $key => $producto) {
            //Verificamos si el item que se itera, cuenta con un 'id' de la base de datos
            if (array_key_exists('id', $producto)) {
                //Actualizar el registro en la BD
                DetallesVentaProducto::withTrashed()
                    ->where('id', $producto['id'])
                    ->update([
                        'cantidad' => $producto['cantidad'],
                        'subtotal' => $producto['subtotal'],
                        'id_cancelacion' => $producto['id_cancelacion'],
                        'motivo_cancelacion' => $producto['motivo_cancelacion'],
                        'deleted_at' => now(),
                        'usuario_cancela' => $user->name
                    ]);
                $band = true;   //Actualizar bandera
            }
        }
    }

    //Esta funcion registra la venta en la tabla "ventas"
    private function registrarVenta($venta, $codigopv, $isClosed = false, $tipo_venta)
    {
        //Buscamos cajas disponibles en el punto de venta actual
        $resultCaja = $this->buscarCaja($this->permisospv->clave_punto_venta);
        //Obtenemos la fecha-hora de actual, con una instancia de Carbon.
        $fecha_cierre = now()->format('Y-m-d H:i:s');

        switch ($this->tipo_venta) {
            case 'socio':
                $nombre = $venta['socio']['nombre'] . ' ' . $venta['socio']['apellido_p'] . ' ' . $venta['socio']['apellido_m'];
                break;
            case 'invitado':
                $nombre = $venta['nombre_invitado'];
                break;
            case 'general':
                $nombre = $venta['nombre_p_general'];
                break;
            case 'empleado':
                $nombre = $venta['nombre_empleado'];
                break;
            default:
                $nombre = null;
        }

        //Se registra la venta
        return Venta::create([
            'tipo_venta' => $tipo_venta,
            'id_socio' =>  $venta['socio']['id'] ?? null,
            'nombre' => $nombre,
            'fecha_apertura' => $fecha_cierre,
            'fecha_cierre' => $isClosed ? $fecha_cierre : null,
            'total' => $this->totalVenta,
            'corte_caja' => $resultCaja->corte,
            'clave_punto_venta' => $codigopv,
            'num_comensales' => $venta['no_comensal'],
        ]);
    }

    /**
     * Registra los detalles de los productos en la tabla "detalles_ventas_productos"\
     * Determina si el producto es imprimible\
     * Determina la zona de impresion
     */
    private function registrarProductosVenta($folio, $venta)
    {
        $inicio = now()->format('Y-m-d H:i:s');
        //Obtener venta original (de la BD)
        $result = Venta::find($folio);

        //Mapa de tiempos por chunk (los modificadores heredan el tiempo del producto base)
        $tiemposPorChunk = collect($venta['productosTable'])
            ->filter(fn($p) => !array_key_exists('modif', $p))
            ->pluck('tiempo', 'chunk');

        //Detalles Venta
        foreach ($venta['productosTable'] as $key => $producto) {
            $zona = $this->obtenerZona($producto, $result);
            $tiempo = array_key_exists('modif', $producto) && array_key_exists('chunk', $producto)
                ? ($tiemposPorChunk[$producto['chunk']] ?? null)
                : ($producto['tiempo'] ?? null);
            DetallesVentaProducto::create([
                'chunk' => $producto['chunk'],
                'folio_venta' => $folio,
                'clave_producto' => $producto['clave_producto'],
                'nombre' => $producto['nombre'],
                'cantidad' => $producto['cantidad'],
                'precio' => $producto['precio'],
                'observaciones' => $producto['observaciones'],
                'subtotal' => $producto['subtotal'],
                'inicio' => $inicio,
                'tiempo' => $tiempo,
                'id_estado' => $producto['print_default'] ? PuntosConstants::ID_ESTADO_PRODUCTO_COLA : null,
                'id_zona' => $zona?->id_zona,
                'mesero' => auth()->user()?->name
            ]);
        }
    }

    /**
     * Obtiene la zona de impresion de destino, del producto
     */
    public function obtenerZona(array $producto, Venta $result)
    {
        $zona = null;
        if ($producto['print_default']) {
            //Buscar las zonas de impresion
            $zona = ProductoZona::where([
                ['clave_producto', '=', $producto['clave_producto']],
                ['clave_punto', '=', $result->clave_punto_venta]
            ])->first();
        }
        return $zona;
    }

    //Registra los detalles de los productos en la tabla "detalles_ventas_pagos"
    private function registrarPagosVenta($folio, $venta, $codigopv)
    {
        //Buscamos el punto de venta.
        $puntoVenta = PuntoVenta::find($codigopv);
        //Detalles de Pago
        foreach ($venta['pagosTable'] as $key => $pago) {
            $resultPago = DetallesVentaPago::create([
                'folio_venta' => $folio,
                'id_socio' => $pago['id_socio'],
                'nombre' => $pago['nombre'],
                'monto' => $pago['monto_pago'],
                'propina' => $pago['propina'],
                'id_tipo_pago' => $pago['id_tipo_pago'],
            ]);

            //Verificamos que el tipo de venta sea socio o invitado de socio (para cargarlo al estado de cuenta)
            if ($this->tipo_venta == 'socio' ||  $this->tipo_venta == 'invitado') {
                //Verificamos si el tipo de pago es firma 
                if (strcasecmp('FIRMA', $pago['descripcion_tipo_pago']) == 0) {
                    //Creamos el concepto en el estado de cuenta (con abono 0)
                    EstadoCuenta::create([
                        'id_socio' => $pago['id_socio'],
                        'id_venta_pago' => $resultPago->id,
                        'concepto' => 'NOTA VENTA: ' . $folio . ' - ' . $puntoVenta->nombre,
                        'fecha' => now()->toDateString(),
                        'cargo' => $pago['monto_pago'],
                        'saldo' => $pago['monto_pago'],
                        'consumo' => true
                    ]);
                    //Verificamos si tiene propina
                    if ($pago['propina']) {
                        //Creamos el concepto de la propina en el estado de cuenta
                        EstadoCuenta::create([
                            'id_socio' => $pago['id_socio'],
                            'id_venta_pago' => $resultPago->id,
                            'concepto' => 'PROPINA NOTA VENTA: ' . $folio . ' - ' . $puntoVenta->nombre,
                            'fecha' => now()->toDateString(),
                            'cargo' => $pago['propina'],
                            'saldo' => $pago['propina'],
                            'consumo' => false
                        ]);
                    }
                } else {
                    //Creamos el concepto en el estado de cuenta (con abono total)
                    EstadoCuenta::create([
                        'id_socio' => $pago['id_socio'],
                        'id_venta_pago' => $resultPago->id,
                        'concepto' => 'NOTA VENTA: ' . $folio . ' - ' . $puntoVenta->nombre,
                        'fecha' => now()->toDateString(),
                        'cargo' => $pago['monto_pago'],
                        'abono' => $pago['monto_pago'],
                        'saldo' => 0,
                        'consumo' => true
                    ]);
                }
            }
        }
    }

    private function validarFirma($validated)
    {
        //Verificamos el tipo de venta sea a un socio o invitado del mismo
        if ($this->tipo_venta == 'socio' || $this->tipo_venta == 'invitado') {
            //Verificar si el metodo actual seleccionado es firma.
            $tipo_pago = TipoPago::where('id', $validated['id_pago'])
                ->where('descripcion', 'like', 'FIRMA')
                ->first();
            //Si la consulta devuelve valor diferente de null
            if ($tipo_pago) {
                //Buscamos el socio
                $result = Socio::find($validated['socioPago']['id']);
                //Si el socio no tiene firma
                if (!$result->firma) {
                    throw new Exception("Este socio no tiene firma autorizada", 1);
                }
            }
        }
    }

    private function validarVentaCerrada($folioVenta)
    {
        //Buscamos la venta
        $result = Venta::find($folioVenta);
        //Si la venta tiene fecha de cierre
        if ($result->fecha_cierre) {
            throw new Exception("Esta venta esta cerrada", 1);
        }
    }

    /**
     * Busca La ultima caja abierta en el punto dado
     */
    public function buscarCaja($clave_punto)
    {
        //Buscamos caja abrierta en el punto actual, en el dia actual
        $result = Caja::where('clave_punto_venta', $clave_punto)
            ->whereNull('fecha_cierre')
            ->first();
        //Si no hay caja
        if (!$result) {
            throw new Exception("No hay caja abierta para este punto de venta");
        }
        return  $result;
    }

    private function verificarMontos()
    {
        //Calculamos total de los productos
        $productos = array_filter($this->productosTable, function ($prod) {
            return !array_key_exists('moved', $prod);
        });
        $montoTotalVenta = array_sum(array_column($productos, 'subtotal'));

        //Calculamos total de los pagos
        $montoTotalPago = array_sum(array_column($this->pagosTable, 'monto_pago'));
        //Si los totales no coinciden, error.
        if ($montoTotalPago != $montoTotalVenta) {
            //Mandamos mensaje de sesion al alert
            throw new Exception("El monto total de pago no es el correcto");
        }
    }

    private function limpiarComponente()
    {
        $this->reset(
            'tipo_venta',
            'invitado',
            'nombre_invitado',
            'nombre_empleado',
            'socioSeleccionado',
            'socio',
            'socioPago',
            'id_pago',
            'monto_pago',
            'propina',
            'seachProduct',
            'selected',
            'productosTable',
            'pagosTable',
            'totalVenta',
            'totalPago',
            'totalPropina',
            'no_comensal',
            'para_llevar'
        );
    }

    /**
     * Realiza la mutiplicacion "subtotal = precio * cantidad" de cada producto de la tabla
     */
    public function recalcularSubtotales()
    {
        //Calculamos el subtotal de cada producto de la tabla
        foreach ($this->productosTable as $key => $producto) {
            //Se calcula el subtotal del producto
            $this->productosTable[$key]['subtotal'] = $producto['precio'] * $producto['cantidad'];
        }
        $this->actualizarTotal();
    }

    /**
     * Agrega el NUEVO producto a la tabla
     */
    public function agregarProducto(Producto $producto, int $cantidad, int $chunk, $autoSum = false)
    {
        //Filtramos el producto(de la tabla) que coincida con la clave del producto a ingresar (y no sea modificador)
        $p_filtrado = array_filter($this->productosTable, function ($item) use ($producto) {
            return $item['clave_producto'] == $producto->clave
                && !array_key_exists('modif', $item);
        });
        //Si la opcion de autosuma esta activada, y hay un producto con la misma clave en la tabla
        if ($autoSum && count($p_filtrado)) {
            //Agregar la cantidad el producto segun el indice del producto filtrado
            foreach ($p_filtrado as $key => $value) {
                if ($cantidad <= 0 && abs($cantidad) >= $this->productosTable[$key]['cantidad']) {
                    //Lanzar excepcion
                    throw new Exception("Negativo superior");
                }
                $this->productosTable[$key]['cantidad'] += $cantidad;
                $this->productosTable[$key]['subtotal'] += $cantidad * $producto->precio_con_impuestos;
                break;
            }
        } else {
            //Si no hay producto con la clave (en la tabla) y la cantidad es negativa
            if (count($p_filtrado) == 0 && $cantidad <= 0) {
                //Lanzar excepcion
                throw new Exception("Cant. inicial invalida");
            }
            //Agregar directamente el producto
            $this->productosTable[] = [
                'chunk' => $chunk,
                'clave_producto' => $producto->clave,
                'nombre' => $producto->descripcion,
                'cantidad' => $cantidad,
                'precio' => $producto->precio_con_impuestos,
                'subtotal' => $cantidad * $producto->precio_con_impuestos,
                'observaciones' => '',
                'tiempo' => '1',
                'print_default' => $producto->print_default
            ];
        }
    }

    /**
     * Agrega los modificadores seleccionados a la tabla
     */
    public function agregarModificadores(array $modificadores, int $chunk)
    {
        foreach ($modificadores as $key => $modificador) {
            $this->productosTable[] = [
                'chunk' => $chunk,
                'clave_producto' => $modificador['clave_modificador'],
                'nombre' => $modificador['descripcion'],
                'cantidad' => $modificador['cantidad'],
                'precio' => $modificador['precio'],
                'subtotal' => $modificador['precio'] * $modificador['cantidad'],
                'observaciones' => '',
                'tiempo' => null,
                'modif' => true,
                'print_default' => $modificador['print_default']
            ];
        }
    }

    /**
     * Crea un registo en 'detalles_caja'.
     * Modifica la venta pendiente.
     * Crea registro de la actualizacion en 'correcciones_ventas'
     */
    public function pagarPendiente($venta)
    {
        //Creamos patron de expresion regular
        $patron_pendi = "/PENDIENTE/i";
        $patron_firma = "/FIRMA/i";
        //Validaciones de tipo de pago
        foreach ($this->pagosTable as $key => $pago) {
            //Buscar el metodo de pago que selecciono.
            $tipo_pago = TipoPago::where('id', $pago['id_tipo_pago'])
                ->first();
            //Validar si volvieron a escoger metodo de pago 'pendiente'
            if (preg_match($patron_pendi, $tipo_pago->descripcion)) {
                throw new Exception("No puedes pagar con pendiente denuevo", 2);
            }
            if ($this->tipo_venta == 'socio' || $this->tipo_venta == 'invitado') {
                //Verificar la firma del socio
                if (preg_match($patron_firma, $tipo_pago->descripcion)) {
                    //Buscamos el socio
                    $result = Socio::find($pago['id_socio']);
                    //Si el socio no tiene firma
                    if (!$result->firma) {
                        throw new Exception("El socio " . $pago['id_socio'] . " no tiene firma autorizada", 1);
                    }
                }
            }
        }
        //Validar el monto total del pago y de los productos
        $montoTotalVenta = array_sum(array_column($this->productosTable, 'subtotal'));
        $montoTotalPago =
            array_sum(array_column($this->pagosTable, 'monto_pago')) + array_sum(array_column($this->pagosTable, 'monto'));
        if ($montoTotalPago != $montoTotalVenta) {
            //Mandamos mensaje de sesion al alert
            throw new Exception("El monto total de pago no es el correcto");
        }

        DB::transaction(function () use ($venta) {
            //Buscamos cajas disponibles en el punto de venta actual
            $resultCaja = $this->buscarCaja($this->permisospv->clave_punto_venta);
            //Crear o actualizar los metodos de pago de la venta
            foreach ($this->pagosTable as $key => $pago) {
                if (array_key_exists('id', $pago)) {
                    $detalle_pago = DetallesVentaPago::find($pago['id']);
                    $detalle_pago->id_tipo_pago = $pago['id_tipo_pago'];
                    $detalle_pago->monto = $pago['monto'];
                    $detalle_pago->propina = $pago['propina'] ?: null;
                    $this->updateEstadoCuenta($detalle_pago);
                    $detalle_pago->save();
                } else {
                    $detalle_pago = DetallesVentaPago::create([
                        'folio_venta' => $venta->folio,
                        'id_socio' => $pago['id_socio'],
                        'nombre' => $pago['nombre'],
                        'monto' => $pago['monto_pago'],
                        'propina' => $pago['propina'],
                        'id_tipo_pago' => $pago['id_tipo_pago'],
                    ]);
                    $this->crearEstadoCuenta($detalle_pago, $venta);
                }
            }
            //Crear el detalle de caja
            $this->crearMovimientoCaja(
                $venta,
                $this->pagosTable,
                PuntosConstants::INGRESO_PENDIENTE_KEY,
                $resultCaja->corte,
            );
            //Buscar el motivo de la correccion (venta pendiente)
            $motivo = MotivoCorreccion::where('descripcion', 'like', '%PENDIENTE%')->first();
            //Crear el registro en la tabla 'correcciones_ventas'
            CorreccionVenta::create([
                'user_name' => auth()->user()->name,
                'folio_venta' => $venta->folio,
                'tipo_venta' => $venta->tipo_venta,
                'solicitante_name' => auth()->user()->name,
                'id_motivo' => $motivo->id,
            ]);
        }, 2);
    }

    /**
     * Verifica si los pagos de la venta tienen un id_pago diferente a pendiente.\
     * Agrega una marca para identificar los pagos editables\
     */
    public function verificarPagos()
    {
        //Obtener el metodo de pago pendiente
        $metodo_pago = TipoPago::where("descripcion", "like", "%PENDI%")->first();
        foreach ($this->pagosTable as $key => $p) {
            $this->pagosTable[$key]['editable'] = $p['id_tipo_pago'] == $metodo_pago->id;
        }
    }

    /**
     * Agrega una marca para el ultimo pago editable agregado\
     * Utilizado para las ventas pendientes.
     */
    public function verificarUltimoPago()
    {
        $last_key = array_key_last($this->pagosTable);
        $this->pagosTable[$last_key]['editable'] = true;
    }

    /**
     * Actualiza el estado de cuenta, referente a la venta pendiente pagada con firma.\
     * Modifica el saldo del estado de cuenta, y crea la propina en firma.
     */
    public function updateEstadoCuenta(DetallesVentaPago $detalle_pago)
    {
        $patron_firma = "/FIRMA/i";
        //Buscar el metodo de pago que selecciono.
        $tipo_pago = TipoPago::where('id', $detalle_pago->id_tipo_pago)
            ->first();
        //Verificar si es firma el metodo seleccionado
        if (preg_match($patron_firma, $tipo_pago->descripcion)) {
            //Buscar el estado de cuenta original
            $edo_cuenta = EstadoCuenta::where('id_venta_pago', $detalle_pago->id)
                ->first();
            //Si la propina esta modificada, respecto del original
            if ($detalle_pago->isDirty('propina')) {
                //Crear la propina en el estado de cuenta
                EstadoCuenta::create([
                    'id_venta_pago' => $edo_cuenta->id_venta_pago,
                    'id_socio' => $edo_cuenta->id_socio,
                    'concepto' => "PROPINA " . $edo_cuenta->concepto,
                    'fecha' => $edo_cuenta->fecha,
                    'cargo' => $detalle_pago->propina,
                    'abono' => 0,
                    'saldo' => $detalle_pago->propina,
                ]);
            }
            //Actualizar el saldo del estado de cuenta
            $edo_cuenta->cargo = $detalle_pago->monto;
            $edo_cuenta->abono = 0;
            $edo_cuenta->saldo = $detalle_pago->monto;
            $edo_cuenta->save();
        }
    }

    /**
     * Crea el cargo en el estado de cuenta, respecto a la venta.\
     * Y en caso de propina, la crea (con firma)
     */
    public function crearEstadoCuenta(DetallesVentaPago $detalle_pago, $venta)
    {
        //Buscamos el punto de venta.
        $puntoVenta = PuntoVenta::find($venta->clave_punto_venta);
        $patron_firma = "/FIRMA/i";
        //Buscar el metodo de pago que selecciono.
        $tipo_pago = TipoPago::where('id', $detalle_pago->id_tipo_pago)
            ->first();
        //Verificar si es firma el metodo seleccionado
        if (preg_match($patron_firma, $tipo_pago->descripcion)) {
            //Creamos el concepto en el estado de cuenta (con abono 0)
            EstadoCuenta::create([
                'id_socio' => $detalle_pago->id_socio,
                'id_venta_pago' => $detalle_pago->id,
                'concepto' => 'NOTA VENTA: ' . $detalle_pago->folio_venta . ' - ' . $puntoVenta->nombre,
                'fecha' => $venta->fecha_cierre,
                'cargo' => $detalle_pago->monto,
                'saldo' => $detalle_pago->monto,
                'consumo' => true
            ]);
            //Verificamos si tiene propina
            if ($detalle_pago->propina) {
                //Creamos el concepto de la propina en el estado de cuenta
                EstadoCuenta::create([
                    'id_socio' => $detalle_pago->id_socio,
                    'id_venta_pago' => $detalle_pago->id,
                    'concepto' => 'PROPINA NOTA VENTA: ' . $detalle_pago->folio_venta . ' - ' . $puntoVenta->nombre,
                    'fecha' => $venta->fecha_cierre,
                    'cargo' => $detalle_pago->propina,
                    'saldo' => $detalle_pago->propina,
                    'consumo' => false
                ]);
            }
        } else {
            //Creamos el concepto en el estado de cuenta (con abono total)
            EstadoCuenta::create([
                'id_socio' => $detalle_pago->id_socio,
                'id_venta_pago' => $detalle_pago->id,
                'concepto' => 'NOTA VENTA: ' . $detalle_pago->folio_venta . ' - ' . $puntoVenta->nombre,
                'fecha' => $venta->fecha_cierre,
                'cargo' => $detalle_pago->monto,
                'abono' => $detalle_pago->monto,
                'saldo' => 0,
                'consumo' => true
            ]);
        }
    }
    /**
     * Crea el registro en la tabla 'correcciones_ventas'\
     * En caso de haber eliminaciones de productos
     */
    public function crearCorreccion($folio_venta)
    {
        //Si hay algun producto en la lista para eliminar
        if (count($this->lista_eliminados)) {
            //Buscar la caja abierta
            $caja = $this->buscarCaja($this->permisospv->clave_punto_venta);
            //Buscar el motivo que corresponde a 'ELIMINAR PRODUCTO DE NOTA'
            $motivo = MotivoCorreccion::where('descripcion', 'like', '%ELIMINAR PRODUCTO%')->first();
            //Si no hay motivo registrado en la tabla
            if (!$motivo)
                throw new Exception("No hay motivo de correccion 'ELIMINAR PRODUCTO DE NOTA' en la tabla 'motivos_correcciones' ", 1);
            CorreccionVenta::create([
                'user_name' => auth()->user()->name,
                'corte_caja' => $caja->corte,
                'folio_venta' => $folio_venta,
                'tipo_venta' => $this->tipo_venta,
                'solicitante_name' => auth()->user()->name,
                'id_motivo' => $motivo->id
            ]);
        }
    }

    /**
     * Se encarga de verificar el tipo de stock de cada articulo.
     * Si el stock es valido descuenta en el punto dado
     */
    private function verificarStock($clave_punto, $productos)
    {
        foreach ($productos as $key => $producto) {
            //Buscar los stocks del articulo
            $stock = Stock::where('codigo_catalogo', $producto['codigo_catalogo'])->get();

            //Si cuenta con dos stocks o mas, lanzar excepcion
            if (count($stock) >= 2)
                throw new Exception("El producto " . $producto['nombre'] . " cuenta con dos tipos de stocks");

            //Obtener clave del stock correspondiente al punto
            $clave_stock = AlmacenConstants::PUNTOS_STOCK[$clave_punto];

            //Buscar el stock unitario
            $stock_cantidad = $stock->where('tipo', AlmacenConstants::CANTIDAD_KEY)->first();
            //Buscar el stock de peso
            $stock_peso = $stock->where('tipo', AlmacenConstants::PESO_KEY)->first();

            //Si tiene stock de peso (peso)
            if ($stock_peso) {
                throw new Exception("No se puede descontar " . $producto['nombre'] . " por stock de peso");;
            } elseif ($stock_cantidad) {
                $stock = $stock_cantidad;
            } else {
                //Si no tiene stock de cantidad (unitario), crear el stock
                $stock = Stock::create([
                    'codigo_catalogo' => $producto['codigo_catalogo'],
                    'tipo' => AlmacenConstants::CANTIDAD_KEY
                ]);
            }
            $this->descontarStock($producto, $stock, $clave_stock);
        }
    }

    /**
     * Recibe el producto a descontar, asi como su stock actual.
     * Se encarga de verificar si el producto/articulo es una copa
     * y asi descontar el stock correspondiente.
     */
    private function descontarStock(array $producto, $stock_producto, $clave_stock)
    {
        //Buscar si existe relacion de copeo-botella
        $copeo = Copa::where('codigo_copa', $producto['codigo_catalogo'])->first();
        //Si existe la relacion con una botella
        if ($copeo) {
            //Calcular diferencia de las copas
            $dif = $stock_producto[$clave_stock] - $producto['cantidad'];

            //Verificar el stock de la copa, en el punto dado
            if ($dif < 0) {
                //Buscar el stock de la botella
                $stock_botella = Stock::where('codigo_catalogo', $copeo->codigo_botella)
                    ->where('tipo', AlmacenConstants::CANTIDAD_KEY)
                    ->first();

                //Verificar si el stock de botella no existe (en la BD)
                if (!$stock_botella) {
                    //Crear stock de cantidad (unitario)
                    $stock_botella = Stock::create([
                        'codigo_catalogo' => $copeo['codigo_botella'],
                        'tipo' => AlmacenConstants::CANTIDAD_KEY
                    ]);
                }
                //Calcular la cantidad de botellas necesarias para el copeo
                $cant_botellas = ceil(abs($dif) / $copeo->equivalencia);

                //Descontar las botellas
                $stock_botella[$clave_stock] -= $cant_botellas;
                $stock_botella->save();
                //Aumentar el stock de copas, segun su equivalencia y la cantidad de copas calculadas
                $stock_producto[$clave_stock] += $cant_botellas * $copeo->equivalencia;
                $stock_producto->save();
            }
        }
        //Actualizar el stock
        $stock_producto[$clave_stock] -= $producto['cantidad'];
        $stock_producto->save();
    }

    /**
     * Crea el registro de la venta, en la tabla "detalles_caja".
     * Utilizado para el corte de caja (reporte de ventas)
     */
    public function crearMovimientoCaja(Venta $venta, $detalles_pago, $tipo_movimiento, $corte_nuevo = null)
    {
        //Si el movimiento de caja es de ingreso normal
        if ($tipo_movimiento == PuntosConstants::INGRESO_KEY) {
            foreach ($detalles_pago as $key => $pago) {
                //Crear registro en la tabla (sin la fecha de pago ya que es una venta normal)
                DetallesCaja::create([
                    'corte_caja' => $venta->corte_caja,
                    'folio_venta' => $venta->folio,
                    'id_socio' => $pago['id_socio'],
                    'nombre' => $pago['nombre'],
                    'monto' =>  array_key_exists('monto_pago', $pago) ? $pago['monto_pago'] : $pago['monto'],
                    'propina' => $pago['propina'],
                    'tipo_movimiento' => $tipo_movimiento,
                    'id_tipo_pago' => $pago['id_tipo_pago'],
                    'fecha_venta' => $venta->fecha_apertura,
                ]);
            }
        } else {
            foreach ($detalles_pago as $key => $pago) {
                //Si el pago no es editable, omitir el registro del detalle de caja
                if (!$pago['editable'])
                    continue;
                //Crear registro en la tabla (el movimiento de la venta. en el corte nuevo)
                DetallesCaja::create([
                    'corte_caja' => $corte_nuevo,
                    'folio_venta' => $venta->folio,
                    'id_socio' => $pago['id_socio'],
                    'nombre' => $pago['nombre'],
                    'monto' =>  array_key_exists('monto_pago', $pago) ? $pago['monto_pago'] : $pago['monto'],
                    'propina' => $pago['propina'],
                    'tipo_movimiento' => $tipo_movimiento,
                    'id_tipo_pago' => $pago['id_tipo_pago'],
                    'fecha_venta' => $venta->fecha_apertura,
                    'fecha_pago' => now() //Agregar la fecha de pago (es pendiente)
                ]);
            }
        }
    }
}
