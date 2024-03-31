<?php

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

Route::view('recepcion', 'recepcion')
    ->middleware(['auth'])
    ->name('recepcion');

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
        Route::view('historial','almacen.Traspasos.historial')->name('almacen.traspasos.historial');
    });
});


Route::view('cocina', 'cocina')
    ->middleware(['auth'])
    ->name('cocina');

Route::get('pv/{codigopv?}', function (string $codigopv) {
    return view('puntoVenta',['codigopv'=>$codigopv]);;
})->middleware(['auth'])->name('pv');

require __DIR__ . '/auth.php';
