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




Route::middleware(['api'])->group(function () {

    Route::post('/autorizar', [AutorizacaoController::class, 'autorizar']);           

    Route::group(['prefix' => 'controlid'], function () {               
        //apis que o controlID bate
        Route::get('/push', [ControlIdController::class, 'handlePush']);
        Route::post('/result', [ControlIdController::class, 'handleResult']);      
    });
  

});