<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ControlIdController;
use App\Http\Controllers\AutorizacaoController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rotas que exigem autenticação (usando autenticação de sessão web)
Route::middleware(['web', 'auth'])->group(function () {
    Route::group(['prefix' => 'controle-acesso'], function () {
        Route::get('/autorizacoes', [AutorizacaoController::class, 'apiIndex']);
        Route::get('/localizacoes', [AutorizacaoController::class, 'apiLocalizacoesIndex']);
        Route::get('/autorizacoes/{deviceid}/{userid}', [AutorizacaoController::class, 'getAutorizacaoPorDispositivo'])->where('deviceid', '[0-9]+')->where('userid', '[0-9]+');
        Route::get('/autorizacoes/{status}', [AutorizacaoController::class, 'apiIndex'])->where('status', '[a-zA-Z]+');
        Route::post('/autorizar', [AutorizacaoController::class, 'autorizar']);           
        Route::post('/revogar', [AutorizacaoController::class, 'revogar']);           
        Route::delete('/autorizacoes/{id}', [AutorizacaoController::class, 'destroy']);
    });
});

// Rotas públicas (sem autenticação)
Route::middleware(['api'])->group(function () {
    Route::group(['prefix' => 'controlid'], function () {               
        //apis que o controlID bate
        Route::get('/push', [ControlIdController::class, 'handlePush']);
        Route::post('/result', [ControlIdController::class, 'handleResult']);      
    });
});