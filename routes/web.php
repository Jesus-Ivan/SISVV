<?php

use App\Http\Controllers\AdministracionController;
use App\Http\Controllers\CargosController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\EdoCuentaController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\PuntosController;
use App\Http\Controllers\RecepcionController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\SistemasController;
use App\Http\Controllers\SociosController;
use App\Http\Middleware\SistemasPermisos;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/', 'welcome');

Route::view('home', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('home');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');


Route::prefix('administracion')->middleware(['auth', 'administracion'])->group(function () {
    Route::view('/', 'administracion.index')->name('administracion');
    Route::view('reportes-ordenes', 'administracion.reportes-ordenes')->name('administracion.reportes-ordenes');
    Route::view('detalles-ordenes', 'administracion.detalles-ordenes')->name('administracion.detalles-ordenes');
    Route::view('reportes-ventas', 'administracion.reportes-ventas')->name('administracion.reportes-ventas');
    Route::view('reportes-cobranza', 'administracion.reportes-cobranza')->name('administracion.reportes-cobranza');
    Route::prefix('nominas')->group(function () {
        Route::get('cargar-periodo', [AdministracionController::class, 'cargarPeriodo'])->name('administracion.cargar-p');
        Route::post('cargar-periodo', [AdministracionController::class, 'subirPeriodo'])->name('administracion.cargar-p');
        Route::get('buscar-periodo', [AdministracionController::class, 'buscarPeriodo'])->name('administracion.buscar-p');
        Route::get('imprimir-periodo/{ref}', [ReportesController::class, 'imprimirNomina'])->name('administracion.imprimir-p');
        Route::delete('eliminar-periodo/{ref}', [AdministracionController::class, 'eliminarNomina'])->name('administracion.eliminar-p');
    });
});

Route::prefix('almacen')->middleware(['auth', 'almacen'])->group(function () {
    Route::view('/', 'almacen.index')->name('almacen');

    Route::prefix('articulos')->group(function () {
        Route::view('/', 'almacen.Articulos.articulos')->name('almacen.articulos');
        Route::view('nuevo', 'almacen.Articulos.nuevo-articulo')->name('almacen.articulos.nuevo');
        Route::get('editar/{articulo}', [CatalogoController::class, 'editArticulo'])->name('almacen.articulos.editar');
    });

    Route::prefix('existencias')->group(function () {
        Route::view('/', 'almacen.Existencias.existencias')->name('almacen.existencias');
        Route::view('reporte', 'almacen.Existencias.reporte')->name('almacen.existencias.reporte');
    });

    Route::prefix('requisicion')->group(function () {
        Route::view('/', 'almacen.Requisiciones.requisiciones')->name('almacen.requi');
        Route::view('nueva', 'almacen.Requisiciones.nueva-requisicion')->name('almacen.requi.nueva');
        Route::view('historial', 'almacen.Requisiciones.historial-requisiciones')->name('almacen.requi.historial');
        Route::view('editar/{folio}', 'almacen.Requisiciones.editar-requisicion')->name('almacen.requi.editar');
        //Requiscion de compra (NUEVAS REQUISICIONES) 
        Route::get('ver/{folio}/{order?}', [ReportesController::class, 'verRequi'])->name('almacen.requi.ver');
    });


    Route::prefix('facturas')->group(function () {
        Route::view('/', 'almacen.Facturas.facturas')->name('almacen.facturas');
        Route::view('nuevo-registro', 'almacen.Facturas.nuevo-registro-factura')->name('almacen.facturas.nuevo-registro');
    });

    Route::prefix('inventario-fisico')->group(function () {
        Route::view('/', 'almacen.Inventario.inventario-fisico')->name('almacen.inventario-fisico');
        Route::view('nuevo-inventario', 'almacen.Inventario.nuevo-inventario')->name('almacen.nuevo-inventario');
    });

    Route::view('clasificacion', 'almacen.Clasificacion.clasificacion')->name('almacen.clasificacion');
    Route::view('proveedores', 'almacen.Proveedores.proveedores')->name('almacen.proveedores');
    Route::view('unidades', 'almacen.Unidades.unidades')->name('almacen.unidades');
    Route::view('grupos', 'almacen.Grupos.grupos')->name('almacen.grupos');

    Route::prefix('entradas')->group(function () {
        Route::view('/', 'almacen.Entradas.entradas')->name('almacen.entradas');
        Route::view('nueva', 'almacen.Entradas.nueva-entrada')->name('almacen.entradas.nueva');
        Route::view('historial', 'almacen.Entradas.historial')->name('almacen.entradas.historial');
    });
    Route::prefix('salidas')->group(function () {
        Route::view('/', 'almacen.Salidas.salidas')->name('almacen.salidas');
        Route::view('nueva', 'almacen.Salidas.nueva-salida')->name('almacen.salidas.nueva');
    });
    Route::prefix('traspasos')->group(function () {
        Route::view('/', 'almacen.Traspasos.traspasos')->name('almacen.traspasos');
        Route::view('nuevo', 'almacen.Traspasos.nuevo-traspaso')->name('almacen.traspasos.nuevo');
        Route::view('solicitud', 'almacen.Traspasos.solicitud-traspaso')->name('almacen.traspasos.solicitud');
        Route::view('historial', 'almacen.Traspasos.historial')->name('almacen.traspasos.historial');
    });

    Route::prefix('ordenes')->group(function () {
        Route::view('nueva', 'almacen.ordenes.nueva-orden')->name('almacen.ordenes');
        Route::view('ordenes_realizadas', 'almacen.Ordenes.ordenes_realizadas')->name('almacen.ordenes_realizadas');
        Route::view('historial', 'almacen.Ordenes.historial')->name('almacen.ordenes.historial');
    });

    Route::view('nuevo-costeo', 'almacen.nuevo-costeo')->name('almacen.nuevo');

    Route::prefix('recetas')->group(function () {
        Route::view('/', 'almacen.Recetas.recetas')->name('almacen.recetas');
        Route::view('nueva', 'almacen.Recetas.nueva-receta')->name('almacen.recetas.nueva');
        Route::view('editar', 'almacen.Recetas.editar-receta')->name('almacen.recetas.editar');
    });

    Route::view('mermas', 'almacen.Mermas.mermas')->name('almacen.mermas');

    Route::prefix('productos')->group(function () {
        //view de la tabla de productos
        Route::view('/', 'almacen.Productos.productos')->name('almacen.productos');
        Route::view('/nuevo', 'almacen.Productos.nuevo-producto')->name('almacen.productos.nuevo');
        Route::view('/editar/{clave}', 'almacen.Productos.editar-producto')->name('almacen.productos.editar');
    });

    Route::prefix('insumos')->group(function () {
        //view de la tabla de insumos
        Route::view('/', 'almacen.Insumos.insumos')->name('almacen.insumos');
        Route::view('/nuevo', 'almacen.Insumos.nuevo-insumo')->name('almacen.insumos.nuevo');
        Route::view('/editar/{clave}', 'almacen.Insumos.editar-insumo')->name('almacen.insumos.editar');
    });

    Route::prefix('presentaciones')->group(function () {
        //view de la tabla de presentaciones
        Route::view('/', 'almacen.Presentaciones.presentaciones')->name('almacen.presentaciones');
        Route::view('/nueva', 'almacen.Presentaciones.nueva-presentacion')->name('almacen.presentaciones.nueva');
        Route::view('/editar/{clave}', 'almacen.Presentaciones.editar-presentacion')->name('almacen.presentaciones.editar');
    });

    Route::prefix('documentos')->group(function () {
        Route::get('existencias', [ReportesController::class, 'getExistencias'])->name('almacen.documentos.existencias');
        Route::post('existencias', [ReportesController::class, 'postExistencias'])->name('almacen.documentos.existencias');
        Route::get('tabla-inv-sem', [ReportesController::class, 'getInvSemanal'])->name('almacen.documentos.inv-sem');
        Route::post('tabla-inv-sem', [ReportesController::class, 'postInvSemanal'])->name('almacen.documentos.inv-sem');
    });

    Route::prefix('entradas-v2')->group(function () {
        //view de la tabla de productos
        Route::view('/', 'almacen.Entradas.v2.entrada')->name('almacen.entradav2');
        Route::view('/nueva', 'almacen.Entradas.v2.nueva-entrada')->name('almacen.entradav2.nueva');
    });
});

Route::prefix('recepcion')->middleware(['auth', 'recepcion'])->group(function () {
    Route::view('/', 'recepcion.index')->name('recepcion');
    Route::prefix('ventas')->group(function () {
        Route::get('/', [RecepcionController::class, 'ventasIndex'])->name('recepcion.ventas');
        Route::get('nueva', [RecepcionController::class, 'ventasNueva'])->name('recepcion.ventas.nueva');
        Route::view('reporte', 'recepcion.Ventas.reporte-ventas')->name('recepcion.ventas.reporte');
    });
    Route::prefix('cobros')->group(function () {
        Route::view('/', 'recepcion.Cobros.cobros')->name('recepcion.cobros');
        Route::view('nuevo', 'recepcion.Cobros.nuevo-cobro')->name('recepcion.cobros.nuevo');
        Route::view('reportes', 'recepcion.Cobros.reporte-cobros')->name('recepcion.cobros.reportes');
        Route::get('recibo/{folio}', [ReportesController::class, 'generarRecibo'])->name('recepcion.cobros.recibo');
        Route::get('corte-detalles/{caja}', [ReportesController::class, 'generarCobranzaDetalles'])->name('recepcion.cobros.corte-detalles');
        Route::get('corte-resumen/{caja}', [ReportesController::class, 'generarCobranzaResumen'])->name('recepcion.cobros.corte-resumen');
    });
    Route::prefix('socios')->group(function () {
        Route::view('/', 'recepcion.Socios.socios')->name('recepcion.socios');
        Route::view('nuevo', 'recepcion.Socios.nuevo-socio')->name('recepcion.socios.nuevo');
        Route::get('editar/{socio}', [SociosController::class, 'showEdit'])->name('recepcion.socios.editar');
        Route::get('qr/{socioId}', [ReportesController::class, 'generarQR'])->name('recepcion.socios.qr');
    });
    Route::prefix('edo-cuenta')->group(function () {
        Route::view('/', 'recepcion.Estado-cuenta.estado-cuenta')->name('recepcion.estado');
        Route::get('nuevo-cargo/{socio}', [EdoCuentaController::class, 'showEditEdoCuenta'])->name('recepcion.estado.nuevo');
        Route::get('reporte/{socio}/{tipo}/{vista}/{fInicio}/{fFin}/{option}', [ReportesController::class, 'generarEstadoCuenta'])->name('recepcion.estado.reporte');
    });

    Route::get('reportes', [RecepcionController::class, 'reportesIndex'])->name('recepcion.reportes');
    Route::post('reportes-vencidos', [ReportesController::class, 'vencidos'])->name('recepcion.reportes.vencidos');
    Route::post('reportes-recibos', [ReportesController::class, 'reporteRecibos'])->name('reportes.recibos');
    Route::post('reportes-recibo-socio', [ReportesController::class, 'reporteRecibosSocio'])->name('reportes.recibos-socio');
    Route::view('caja', 'recepcion.caja.caja')->middleware(['auth'])->name('recepcion.caja');
});

Route::prefix('cocina')->middleware(['auth'])->group(function () {
    Route::view('/', 'cocina.index')->name('cocina');

    Route::prefix('ordenes')->group(function () {
        Route::view('/', 'cocina.Ordenes.ordenes')->name('cocina.ordenes');
        Route::view('ver/{folio}', 'cocina.Ordenes.ver')->name('cocina.ver');
        Route::view('historial', 'cocina.Ordenes.historial')->name('cocina.ordenes.historial');
    });

    Route::view('inventarios', 'cocina.Inventarios.inventarios')->name('cocina.inventarios');

    Route::prefix('solicitud-mercancia')->group(function () {
        Route::view('/', 'cocina.Inventarios.SolicitarMercancia.mercancias')->name('cocina.mercancias');
        Route::view('nueva', 'cocina.Inventarios.SolicitarMercancia.nueva-mercancia')->name('cocina.mercancias.nueva-soli');
    });

    Route::view('platillos', 'cocina.Platillos.platillos')->name('cocina.platillos');

    Route::prefix('transformaciones')->group(function () {
        Route::view('/', 'cocina.Transformaciones.transformaciones')->name('cocina.transformaciones');
        Route::view('nueva', 'cocina.Transformaciones.nueva-transformacion')->name('cocina.transformaciones.nueva');
        Route::view('historial', 'cocina.Transformaciones.historial-transformaciones')->name('cocina.transformaciones.historial');
    });

    Route::view('mermas', 'cocina.Mermas.mermas')->name('cocina.mermas');
});

Route::prefix('pv/{codigopv}')->middleware(['auth', 'puntos'])->group(function () {

    Route::get('/', [PuntosController::class, 'index'])->name('pv.index');

    Route::prefix('ventas')->group(function () {
        Route::get('/', [PuntosController::class, 'ventasIndex'])->name('pv.ventas');
        Route::get('/editar/{folioventa}', [PuntosController::class, 'editarVenta'])->name('pv.ventas.editar');
        Route::get('/ver/{folioventa}', [PuntosController::class, 'verVenta'])->name('pv.ventas.ver');
        Route::get('nueva', [PuntosController::class, 'nuevaVenta'])->name('pv.ventas.nueva');
        Route::get('reporte', [PuntosController::class, 'reporteVentas'])->name('pv.ventas.reporte');
    });


    Route::get('inventario', [PuntosController::class, 'verInventario'])->name('pv.inventario');
    Route::get('prod-vendidos', [PuntosController::class, 'prodVendidos'])->name('pv.prod-vendidos');
    Route::get('salidas', [PuntosController::class, 'salidas'])->name('pv.salidas');


    Route::get('socios', [PuntosController::class, 'verSocios'])->name('pv.socios');
    Route::get('caja', [PuntosController::class, 'caja'])->middleware(['auth'])->name('pv.caja');
});

Route::prefix('sistemas')->middleware(['auth', 'sistemas'])->group(function () {
    Route::view('/', 'sistemas.index')->name('sistemas');

    //DEPARTAMENTO DE ALMACEN
    Route::prefix('almacen')->group(function () {
        Route::view('catalogo', 'sistemas.Almacen.catalogo')->name('sistemas.catalogo');
        Route::view('catalogo/nuevo', 'sistemas.Almacen.nuevo-catalogo')->name('sistemas.almacen.nuevo');
        Route::get('reporte-entradas', [SistemasController::class, 'repEntradas'])->name('sistemas.almacen.reporte-entradas');
        Route::post('reporte-entradas', [ReportesController::class, 'repEntradas'])->name('sistemas.almacen.reporte-entradas');


        Route::view('proveedores', 'sistemas.proveedores')->name('sistemas.proveedores');
        Route::view('familias', 'sistemas.familias')->name('sistemas.familias');
        Route::view('categorias', 'sistemas.categorias')->name('sistemas.categorias');
        Route::view('unidades', 'sistemas.unidades')->name('sistemas.unidades');
    });

    //DEPARTAMENTO DE PUNTOS DE VENTA
    Route::prefix('PV')->group(function () {
        Route::get('prod-vendidos', [SistemasController::class, 'prodVendidos'])->name('sistemas.pv.prod-vendidos');
        Route::view('notas', 'sistemas.Puntos.notas')->name('sistemas.pv.notas');
        Route::get('/editar/{folioventa}', [SistemasController::class, 'editarVenta'])->name('sistemas.pv.editar');
    });

    //DEPARTAMENTO DE RECEPCIÓN
    Route::view('membresias', 'sistemas.Recepcion.membresias')->name('sistemas.membresias');

    //HERRAMIENTAS ADICIONALES A SISTEMAS
    Route::prefix('registros')->group(function () {
        Route::view('/', 'sistemas.Herramientas.registros')->name('sistemas.registros');
        Route::post('/', [ExcelController::class, 'importData'])->name('subirRegistros');
    });
    Route::prefix('reportes')->group(function () {
        Route::view('/', 'sistemas.Herramientas.reportes')->name('sistemas.reportes');
        Route::post('/ventas', [ReportesController::class, 'ventasMes'])->name('sistemas.reportes.ventas');
        Route::post('/recibos-mes', [ReportesController::class, 'recibosMes'])->name('sistemas.reportes.recibos');
        Route::post('/socios-actuales', [ReportesController::class, 'socios'])->name('sistemas.reportes.socios');
    });

    //RECEPCION
    Route::prefix('recepcion')->group(function () {
        Route::view('/cargo-mensualidades', 'sistemas.Recepcion.cargo-mensualidades')->name('sistemas.cargoMensualidades');
        Route::post('/cargo-mensualidades', [CargosController::class, 'cargarMensualidades'])->name('sistemas.cargoMensualidades');
        Route::view('/recargos', 'sistemas.Recepcion.recargos')->name('sistemas.recargos');
        Route::post('/recargos', [CargosController::class, 'calcularRecargos'])->name('sistemas.recargos');
        Route::view('/cargo-anualidades', 'sistemas.Recepcion.cargo-anualidades')->name('sistemas.cargoAnualidades');
        Route::view('/cargo-dif-consumos', 'sistemas.Recepcion.cargo-diferencias')->name('sistemas.cargoDifConsumos');
        Route::post('/cargo-dif-consumos', [CargosController::class, 'cargarDiferencias'])->name('sistemas.cargoDifConsumos');
    });
});

//PORTICO
Route::prefix('acceso')->middleware(['auth', 'acceso'])->group(function () {
    Route::view('/', 'acceso.index')->name('acceso');
    Route::view('socios', 'acceso.Socios.principal')->name('acceso.socios');
});

Route::get('venta/ticket/{venta}', [ReportesController::class, 'generarTicket'])->name('ventas.ticket');
Route::get('ventas/corte/{caja}/{codigopv?}', [ReportesController::class, 'generarCorte'])->name('ventas.corte');
//Ordenes de compra (VIEJAS REQUISICIONES)
Route::get('ordenes/{folio}/{order?}', [ReportesController::class, 'generarRequisicion'])->name('orden');
//Reporte de existencias actuales
Route::post('reporte-existencias', [ReportesController::class, 'generarReporteExistencias'])->name('reporte-existencias');
//Esta ruta debe moverse al departamento de sistemas. cuando almacen e inventarios esten listos
Route::post('/prod-vendidos', [SistemasController::class, 'getReporteVendidos'])->name('prod-vendidos');


require __DIR__ . '/auth.php';
