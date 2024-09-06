<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\NoteController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('register',  [AuthController::class, 'register']);
    Route::apiResource('notes', NoteController::class);
    Route::apiResource('categories', CategoryController::class);
});
