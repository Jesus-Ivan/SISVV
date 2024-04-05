<?php

use GuzzleHttp\Psr7\Request;
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

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::prefix('almacen')->middleware(['auth'])->group(function () {
    Route::view('/','almacen.index')->name('almacen');
    
    Route::view('articulos','almacen.articulos')->name('almacen.articulos');
    Route::view('asignar','almacen.asignar')->name('almacen.asignar');
    Route::view('proveedores','almacen.proveedores')->name('almacen.proveedores');
    Route::view('familias','almacen.familias')->name('almacen.familias');
    Route::view('categorias','almacen.categorias')->name('almacen.categorias');
    Route::view('unidades','almacen.unidades')->name('almacen.unidades');

    Route::prefix('entradas')->group(function(){
        Route::view('/','almacen.Entradas.entradas')->name('almacen.entradas');
        Route::view('historial','almacen.Entradas.historial')->name('almacen.entradas.historial');
    });
    Route::prefix('salidas')->group(function(){
        Route::view('/','almacen.Salidas.salidas')->name('almacen.salidas');
        Route::view('nueva','almacen.Salidas.nueva-salida')->name('almacen.salidas.nueva');
    });
    Route::prefix('traspasos')->group(function(){
        Route::view('/','almacen.Traspasos.traspasos')->name('almacen.traspasos');
        Route::view('nuevo','almacen.Traspasos.nuevo-traspaso')->name('almacen.traspasos.nuevo');
        Route::view('solicitud','almacen.Traspasos.solicitud-traspaso')->name('almacen.traspasos.solicitud');
        Route::view('historial','almacen.Traspasos.historial')->name('almacen.traspasos.historial');
    });

    Route::view('ordenes','almacen.ordenes')->name('almacen.ordenes');
    Route::view('editar-orden','almacen.editar-orden')->name('almacen.editar');
    Route::prefix('ordenes_realizadas')->group(function(){
        Route::view('/','almacen.Ordenes.ordenes_realizadas')->name('almacen.ordenes_realizadas');
        Route::view('historial','almacen.Ordenes.historial')->name('almacen.ordenes.historial');
    });

    Route::view('nuevo-costeo','almacen.nuevo-costeo')->name('almacen.nuevo');
    
    Route::prefix('recetas')->group(function(){
        Route::view('/','almacen.Recetas.recetas')->name('almacen.recetas');
        Route::view('nueva','almacen.Recetas.nueva-receta')->name('almacen.recetas.nueva');
        Route::view('editar','almacen.Recetas.editar-receta')->name('almacen.recetas.editar');
    });
});

Route::prefix('recepcion')->middleware(['auth'])->group(function (){
    Route::view('/','recepcion.index')->name('recepcion');
    Route::prefix('ventas')->group(function(){
        Route::view('/','recepcion.Ventas.ventas')->name('recepcion.ventas');
        Route::view('nueva','recepcion.Ventas.nueva-venta')->name('recepcion.ventas.nueva');
        Route::view('reporte','recepcion.Ventas.reporte-ventas')->name('recepcion.ventas.reporte');
    });
    Route::prefix('cobros')->group(function(){
        Route::view('/','recepcion.Cobros.cobros')->name('recepcion.cobros');
        Route::view('nuevo','recepcion.Cobros.nuevo-cobro')->name('recepcion.cobros.nuevo');
        Route::view('reportes','recepcion.Cobros.reporte-cobros')->name('recepcion.cobros.reportes');
    });
    Route::prefix('socios')->group(function(){
        Route::view('/','recepcion.Socios.socios')->name('recepcion.socios');
        Route::view('nuevo','recepcion.Socios.nuevo-socio')->name('recepcion.socios.nuevo');
        Route::view('editar/{socio}','recepcion.Socios.editar-socio')->name('recepcion.socios.editar');
    });
    Route::prefix('edo-cuenta')->group(function(){
        Route::view('/','recepcion.Estado-cuenta.estado-cuenta')->name('recepcion.estado');
        Route::view('nuevo-cargo','recepcion.Estado-cuenta.nuevo-cargo')->name('recepcion.estado.nuevo');
    });

});

Route::view('cocina', 'cocina')
    ->middleware(['auth'])
    ->name('cocina');

Route::get('pv/{codigopv?}', function (string $codigopv) {
    return view('puntoVenta', ['codigopv' => $codigopv]);;
})->middleware(['auth'])->name('pv');

require __DIR__ . '/auth.php';
