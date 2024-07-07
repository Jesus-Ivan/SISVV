<?php

use App\Http\Controllers\CargosController;
use App\Http\Controllers\EdoCuentaController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\PermisosController;
use App\Http\Controllers\PuntosController;
use App\Http\Controllers\RecepcionController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\SociosController;
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


Route::prefix('administracion')->middleware(['auth'])->group(function () {
    Route::view('/', 'administracion.index')->name('administracion');
    Route::view('reportes-ordenes', 'administracion.reportes-ordenes')->name('administracion.reportes-ordenes');
    Route::view('detalles-ordenes', 'administracion.detalles-ordenes')->name('administracion.detalles-ordenes');
    Route::view('reportes-ventas', 'administracion.reportes-ventas')->name('administracion.reportes-ventas');
    Route::view('reportes-cobranza', 'administracion.reportes-cobranza')->name('administracion.reportes-cobranza');
});

Route::prefix('almacen')->middleware(['auth'])->group(function () {
    Route::view('/', 'almacen.index')->name('almacen');

    Route::view('articulos', 'almacen.articulos')->name('almacen.articulos');
    Route::view('asignar', 'almacen.asignar')->name('almacen.asignar');
    Route::view('proveedores', 'almacen.proveedores')->name('almacen.proveedores');
    Route::view('familias', 'almacen.familias')->name('almacen.familias');
    Route::view('categorias', 'almacen.categorias')->name('almacen.categorias');
    Route::view('unidades', 'almacen.unidades')->name('almacen.unidades');

    Route::prefix('entradas')->group(function () {
        Route::view('/', 'almacen.Entradas.entradas')->name('almacen.entradas');
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

    Route::view('ordenes', 'almacen.ordenes')->name('almacen.ordenes');
    Route::view('editar-orden', 'almacen.editar-orden')->name('almacen.editar');
    Route::prefix('ordenes_realizadas')->group(function () {
        Route::view('/', 'almacen.Ordenes.ordenes_realizadas')->name('almacen.ordenes_realizadas');
        Route::view('historial', 'almacen.Ordenes.historial')->name('almacen.ordenes.historial');
    });

    Route::view('nuevo-costeo', 'almacen.nuevo-costeo')->name('almacen.nuevo');

    Route::prefix('recetas')->group(function () {
        Route::view('/', 'almacen.Recetas.recetas')->name('almacen.recetas');
        Route::view('nueva', 'almacen.Recetas.nueva-receta')->name('almacen.recetas.nueva');
        Route::view('editar', 'almacen.Recetas.editar-receta')->name('almacen.recetas.editar');
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

    Route::view('cartera', 'recepcion.Cartera.vencidos')->name('recepcion.cartera');
    Route::post('cartera', [ReportesController::class, 'vencidos'])->name('recepcion.cartera.vencidos');

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

    Route::view('inventario', 'puntos.Inventario.inventario')->name('pv.inventario');

    Route::prefix('solicitudes-mercancia')->group(function () {
        Route::view('/', 'puntos.Inventario.SolicitarMercancia.solicitudes')->name('pv.mercancia');
        Route::view('/ver/{folio}', 'puntos.Inventario.SolicitarMercancia.ver-solicitud')->name('pv.mercancia.ver');
        Route::view('nueva', 'puntos.Inventario.SolicitarMercancia.nueva-solicitud')->name('pv.mercancia.nueva');
    });

    Route::get('socios', [PuntosController::class, 'verSocios'])->name('pv.socios');
    Route::get('caja', [PuntosController::class, 'caja'])->middleware(['auth'])->name('pv.caja');
});

Route::prefix('sistemas')->middleware(['auth', 'sistemas'])->group(function () {
    Route::view('/', 'sistemas.index')->name('sistemas');

    //DEPARTAMENTO DE ALMACEN
    Route::prefix('catalogo')->group(function () {
        Route::view('/', 'sistemas.Almacen.catalogo')->name('sistemas.catalogo');
        Route::view('nuevo', 'sistemas.Almacen.nuevo-catalogo')->name('sistemas.almacen.nuevo');
    });
    Route::view('proveedores', 'sistemas.proveedores')->name('sistemas.proveedores');
    Route::view('familias', 'sistemas.familias')->name('sistemas.familias');
    Route::view('categorias', 'sistemas.categorias')->name('sistemas.categorias');
    Route::view('unidades', 'sistemas.unidades')->name('sistemas.unidades');

    //DEPARTAMENTO DE RECEPCIÓN
    Route::view('membresias', 'sistemas.Recepcion.membresias')->name('sistemas.membresias');

    //HERRAMIENTAS ADICIONALES A SISTEMAS
    Route::prefix('registros')->group(function () {
        Route::view('/', 'sistemas.Herramientas.registros')->name('sistemas.registros');
        Route::post('/', [ExcelController::class, 'importData'])->name('subirRegistros');
    });
    Route::prefix('reportes')->group(function () {
        Route::view('/', 'sistemas.Herramientas.reportes')->name('sistemas.sistemas');
        Route::post('/', [ReportesController::class, 'mensual'])->name('sistemas.reportes');
    });
    //Reporte de socios en excel
    Route::get('socios', [ReportesController::class, 'socios'])->name('sistemas.socios');

    //RECEPCION
    Route::prefix('recepcion')->group(function () {
        Route::view('/cargo-mensualidades', 'sistemas.Recepcion.cargo-mensualidades')->name('sistemas.cargoMensualidades');
        Route::post('/cargo-mensualidades', [CargosController::class, 'cargarMensualidades'])->name('sistemas.cargoMensualidades');
        Route::view('/recargos', 'sistemas.Recepcion.recargos')->name('sistemas.recargos');
        Route::post('/recargos', [CargosController::class, 'calcularRecargos'])->name('sistemas.recargos');
        Route::view('/cargo-anualidades', 'sistemas.Recepcion.cargo-anualidades')->name('sistemas.cargoAnualidades');
        Route::post('/verificar-anu', [CargosController::class, 'verificarAnualidades'])->name('sistemas.verificarAnualidades');
    });
});

Route::prefix('portico')->middleware(['auth'])->group(function () {
    Route::view('/', 'portico.index')->name('portico');
    Route::view('socios', 'portico.Socios.container')->name('portico.socios');
});

Route::get('venta/ticket/{venta}', [ReportesController::class, 'generarTicket'])->name('ventas.ticket');
Route::get('ventas/corte/{caja}', [ReportesController::class, 'generarCorte'])->middleware(['auth'])->name('ventas.corte');


require __DIR__ . '/auth.php';
