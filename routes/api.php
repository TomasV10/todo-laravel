<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\AuthController;

Route::middleware('auth:api')->group(function() {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
Route::get('/todos', [TodoController::class, 'index']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/todos', [TodoController::class, 'store']);
Route::patch('/todos/{todo}', [TodoController::class, 'update']);
Route::delete('/todos/{todo}', [TodoController::class, 'destroy']);
Route::patch('/todosCheckAll', [TodoController::class, 'updateAll']);
Route::delete('/todosDeleteCompleted', [TodoController::class, 'destroyCompleted']);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/userList', [AuthController::class, 'index']);
Route::patch('/userList/{user}', [AuthController::class, 'update']);
Route::delete('/userList/{user}', [AuthController::class, 'destroy']);






