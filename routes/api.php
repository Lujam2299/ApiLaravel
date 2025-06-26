<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Gastos\GastosController;
use App\Http\Controllers\Turnos\TurnosController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MessageController;
// rutas publicas
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);






//rutas de auteticacion con santum protegidas
// estas se utlizan unicamente para usuiroos que esten logeados mediante credenciales validas

Route::middleware('auth:sanctum')->group(function () {

    //rutas de auteticacion
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', fn(Request $request) => $request->user());
    //rutas de gastos y turnos(entrada y salida)
    Route::post('/guardarTurno', [TurnosController::class, 'guardarTurno']);

    Route::post('/guardarGastos', [GastosController::class, 'guardarGastos']);

    //rutas de ubicacion
    Route::post('/locations', [LocationController::class, 'store']);

    
 // Mensajería
    Route::get('/messages/search-users', [MessageController::class, 'searchUsers']);
    Route::post('/messages/start-conversation', [MessageController::class, 'startConversation']);
    Route::post('/messages/send', [MessageController::class, 'sendMessage']);
    Route::get('/messages/{conversation}', [MessageController::class, 'getMessages']);
    Route::post('/messages/{message}/read', [MessageController::class, 'markAsRead']);
    Route::get('/conversations', [MessageController::class, 'getConversations']);
    
});
