<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProformaController;
use App\Http\Controllers\Api\CronogramaController;
use App\Http\Controllers\Api\BancoController;
use App\Http\Controllers\Api\TipoFinanciamientoController;
use App\Http\Controllers\Api\PagoSeparacionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas para proforma
Route::get('/proforma/{proforma}/cronograma-data', [ProformaController::class, 'getCronogramaData']);

// Rutas para cronograma
Route::get('/cronograma/tipos-cuota', [CronogramaController::class, 'getTiposCuota']);
Route::get('/cronograma/estados-cuota', [CronogramaController::class, 'getEstadosCuota']);

// Rutas para bancos
Route::get('/bancos', [BancoController::class, 'index']);

// Rutas para tipos de financiamiento
Route::get('/tipos-financiamiento', [TipoFinanciamientoController::class, 'index']);

// Rutas para tipos de comprobante
Route::get('/tipos-comprobante', [CronogramaController::class, 'getTiposComprobante']);

// Rutas para pagos de separación
Route::prefix('pagos-separacion')->group(function () {
    Route::get('/{separacion_id}', [PagoSeparacionController::class, 'index']);
    Route::get('/proforma/{proforma_id}', [PagoSeparacionController::class, 'getByProforma']);
    Route::post('/', [PagoSeparacionController::class, 'store']);
    Route::post('/batch', [PagoSeparacionController::class, 'storeBatch']);
    Route::delete('/{id}', [PagoSeparacionController::class, 'destroy']);
    Route::get('/comprobante/{id}', [PagoSeparacionController::class, 'descargarComprobante']);
});

// Ruta para obtener información de separación
Route::get('/separacion/{separacion_id}/info', [PagoSeparacionController::class, 'getSeparacionInfo']);

// Rutas para datos auxiliares de pagos de separación
Route::get('/monedas', [PagoSeparacionController::class, 'getMonedas']);
Route::get('/medios-pago', [PagoSeparacionController::class, 'getMediosPago']);
Route::get('/cuentas-bancarias', [PagoSeparacionController::class, 'getCuentasBancarias']);
