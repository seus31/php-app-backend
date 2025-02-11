<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\NoteController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register',  [AuthController::class, 'register']);
    Route::post('login',  [AuthController::class, 'login']);
});

Route::prefix('v1')->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('check/token', [AuthController::class, 'checkToken']);
        Route::post('logout',  [AuthController::class, 'logout']);
        Route::apiResource('notes', NoteController::class);
        Route::apiResource('categories', CategoryController::class);
    });
});
