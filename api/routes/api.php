<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SduiActionController;
use App\Http\Controllers\SduiScreenController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/sdui/screens/{name}', [SduiScreenController::class, 'show']);
    Route::post('/sdui/actions/{name}', [SduiActionController::class, 'store']);
});
