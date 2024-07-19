<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\NoteController;

Route::prefix('v1')->group(function () {
    Route::apiResource('notes', NoteController::class);
});
