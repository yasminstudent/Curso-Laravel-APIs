<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;

Route::get('/ping', function (){
    return ["pong" => true];
});
Route::get('/unauthenticated', function (){
    return ['error' => 'Usuário não logado!'];
})->name('login');

Route::post('/user', [AuthController::class, 'create']);

//Login com Sanctum
Route::post('/auth', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->get('/auth/logout', [AuthController::class, 'logout']);

// o middleware fica assim: middleware('auth:sanctum')

//Login com JWT
Route::post('/auth/login', [AuthController::class, 'loginJWT']);
Route::middleware('auth:api')->get('/auth/jwt/logout', [AuthController::class, 'logoutJWT']);
Route::middleware('auth:api')->get('/auth/jwt/me', [AuthController::class, 'me']);
// o middleware fica assim: middleware('auth:api')

//CRUD das tarefas
Route::middleware('auth:api')->post('/todo', [ApiController::class, 'create']);
Route::get('/todo', [ApiController::class, 'index']);
Route::get('/todo/{id}', [ApiController::class, 'show']);
Route::middleware('auth:api')->put('/todo/{id}', [ApiController::class, 'update']);
Route::middleware('auth:api')->delete('/todo/{id}', [ApiController::class, 'destroy']);

//Upload de imagem
Route::post('/upload', [ApiController::class, 'upload']);
