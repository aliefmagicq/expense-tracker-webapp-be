<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('sign-up', [AuthController::class, 'signUp']);
    Route::post('sign-in', [AuthController::class, 'signIn']);

    Route::middleware(['auth:sanctum', 'check_user', 'check_role:user,supervisor'])->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('sign-out', [AuthController::class, 'signOut']);
    });
});
