<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiSyncController;
use App\Http\Controllers\Api\ApiVentaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [ApiAuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ApiAuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Módulo Ajustes / Sincronización
    Route::get('/cajas/activas', [ApiSyncController::class, 'getCajasActivas']);
    Route::get('/sync/socios', [ApiSyncController::class, 'syncSocios']);
    Route::get('/sync/productos', [ApiSyncController::class, 'syncProductos']);
    Route::get('/sync/tipos-venta', [ApiSyncController::class, 'getTiposVenta']);

    // Tipos de pago
    Route::get('/tipos-pago', [ApiSyncController::class, 'getTiposPago']);

    // Módulo Ventas
    Route::get('/ventas', [ApiVentaController::class, 'index']);
    Route::get('/ventas/{folio}', [ApiVentaController::class, 'show']);
    Route::post('/ventas', [ApiVentaController::class, 'store']);
    Route::post('/ventas/{folio}/productos', [ApiVentaController::class, 'appendProductos']);
    Route::post('/ventas/{folio}/transferir-producto', [ApiVentaController::class, 'transferirProducto']);

});

Route::get('/hola', function () {
    return response()->json(['message' => '¡Hola desde la API!']);
    Route::post('/ventas/{folio}/reimprimir', [ApiVentaController::class, 'reimprimir']);

});

    Route::get('/hola', function () {
        return response()->json(['message' => '¡Hola desde la API!']);
    });