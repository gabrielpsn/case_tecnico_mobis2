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

// Rotas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Rotas de API Resource (colocadas primeiro para evitar conflitos)
    Route::apiResources([
        'motoristas' => \App\Http\Controllers\MotoristaController::class,
        'veiculos' => \App\Http\Controllers\VeiculoController::class,
        'manutencoes' => \App\Http\Controllers\ManutencaoController::class,
    ]);

    // Rotas de telemetria (agrupadas por funcionalidade)
    Route::prefix('telemetria')->group(function () {
        // Rota para buscar telemetria de todos os veículos
        Route::get('veiculos/telemetry', [\App\Http\Controllers\VeiculoLocalizacaoController::class, 'getTelemetry']);

        // Rota para buscar telemetria detalhada de um veículo específico
        Route::get('veiculos/{id}', [\App\Http\Controllers\VeiculoLocalizacaoController::class, 'getVeiculoTelemetry']);

        // Rotas de localização
        Route::post('localizacao', [\App\Http\Controllers\VeiculoLocalizacaoController::class, 'store']);
        Route::get('veiculos/{veiculoId}/localizacao', [\App\Http\Controllers\VeiculoLocalizacaoController::class, 'show']);

        // Rotas de status
        Route::post('status', [\App\Http\Controllers\VeiculoStatusController::class, 'store']);
        Route::get('veiculos/{veiculoId}/status', [\App\Http\Controllers\VeiculoStatusController::class, 'show']);
    });

    // Rotas adicionais
    Route::get('veiculos/{veiculo}/manutencoes', [\App\Http\Controllers\ManutencaoController::class, 'porVeiculo']);
});
