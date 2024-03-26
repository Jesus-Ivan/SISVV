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
Route::view('almacen', 'almacen')
    ->middleware(['auth'])
    ->name('almacen');
Route::view('cocina', 'cocina')
    ->middleware(['auth'])
    ->name('cocina');

Route::get('pv/{codigopv?}', function (string $codigopv) {
    return view('puntoVenta',['codigopv'=>$codigopv]);;
})->middleware(['auth'])->name('pv');

require __DIR__ . '/auth.php';
