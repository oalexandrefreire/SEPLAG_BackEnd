<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CidadeController;
use App\Http\Controllers\FotoPessoaController;
use App\Http\Controllers\LotacaoController;
use App\Http\Controllers\ServidorEfetivoController;
use App\Http\Controllers\ServidorTemporarioController;
use App\Http\Controllers\UnidadeController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/refresh-token', [AuthController::class, 'refreshToken']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/revogar-tokens', [AuthController::class, 'logout']);
    Route::apiResource('servidor-efetivo', ServidorEfetivoController::class);
    Route::get('/servidor-efetivos/unidade/{unid_id}', [ServidorEfetivoController::class, 'servidoresPorUnidade']);
    Route::get('/servidor-efetivos/endereco', [ServidorEfetivoController::class, 'enderecoPorNome']);
    Route::apiResource('servidor-temporario', ServidorTemporarioController::class);
    Route::post('/foto/upload/{pes_id}', [FotoPessoaController::class, 'upload']);
    Route::get('/foto/{pes_id}', [FotoPessoaController::class, 'show']);
    Route::apiResource('unidade', UnidadeController::class);
    Route::apiResource('lotacao', LotacaoController::class);
    Route::apiResource('cidade', CidadeController::class);
});
