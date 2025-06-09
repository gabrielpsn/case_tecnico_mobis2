<?php

use App\Http\Controllers\AuthController;
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

// Rotas públicas
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    // Rotas protegidas aqui
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Rotas para motoristas
    Route::apiResource('motoristas', \App\Http\Controllers\MotoristaController::class);

    // Rotas para veículos
    Route::apiResource('veiculos', \App\Http\Controllers\VeiculoController::class);

    // Rotas para manutenções
    Route::apiResource('manutencoes', \App\Http\Controllers\ManutencaoController::class);
    Route::get('veiculos/{veiculo}/manutencoes', [\App\Http\Controllers\ManutencaoController::class, 'porVeiculo']);

    // Rotas para telemetria dos veículos
    Route::prefix('telemetria')->group(function () {
        // Rotas para localização do veículo
        Route::post('localizacao', [\App\Http\Controllers\VeiculoLocalizacaoController::class, 'store']);
        Route::get('veiculos/{veiculoId}/localizacao', [\App\Http\Controllers\VeiculoLocalizacaoController::class, 'show']);

        // Rotas para status do veículo
        Route::post('status', [\App\Http\Controllers\VeiculoStatusController::class, 'store']);
        Route::get('veiculos/{veiculoId}/status', [\App\Http\Controllers\VeiculoStatusController::class, 'show']);
    });
});
