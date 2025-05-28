<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Gastos\GastosController;


// rutas publicas
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);


//rutas de auteticacion con santum protegidas
// estas se utlizan unicamente para usuiroos que esten logeados mediante credenciales validas

Route::middleware('auth:sanctum')->group(function () {

    //rutas de auteticacion
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', fn(Request $request) => $request->user());
    Route::post('/guaradarGastos', [GastosController::class, 'guaradarGastos']);
});
