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
    });
});

Route::view('almacen', 'almacen')
    ->middleware(['auth'])
    ->name('almacen');  
Route::view('cocina', 'cocina')
    ->middleware(['auth'])
    ->name('cocina');

Route::get('pv/{codigopv?}', function (string $codigopv) {
    return view('puntoVenta', ['codigopv' => $codigopv]);;
})->middleware(['auth'])->name('pv');

require __DIR__ . '/auth.php';
