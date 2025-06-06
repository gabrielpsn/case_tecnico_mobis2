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

    Route::apiResource('motoristas', \App\Http\Controllers\MotoristaController::class);
    Route::apiResource('veiculos', \App\Http\Controllers\VeiculoController::class);

    Route::apiResource('manutencoes', \App\Http\Controllers\ManutencaoController::class);
    Route::get('veiculos/{veiculo}/manutencoes', [\App\Http\Controllers\ManutencaoController::class, 'porVeiculo']);
});
