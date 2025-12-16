<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DailyBalanceController;
use App\Http\Controllers\InitialBalanceController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\TransactionController;
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

Route::prefix('organization')->group(function () {
   Route::middleware(['auth:sanctum', 'check_user', 'check_role:supervisor'])->group(function () {
       Route::post('/', [OrganizationController::class, 'create']);

       /**
        * Usefull for dashboard statistic front end
        */
       Route::get('/', [OrganizationController::class, 'getOrganizations']);
   }); 
});

Route::prefix('branch')->group(function () {
   Route::middleware(['auth:sanctum', 'check_user', 'check_role:supervisor'])->group(function () {
       Route::post('/', [BranchController::class, 'create']);
       Route::get('/', [BranchController::class, 'getBranches']);
   }); 
});

Route::prefix('initial-balance')->group(function () {
   Route::middleware(['auth:sanctum', 'check_user', 'check_role:supervisor'])->group(function () {
       Route::post('/', [InitialBalanceController::class, 'create']);
       Route::get('/', [InitialBalanceController::class, 'getInitialBalances']);
   }); 
});

Route::prefix('transaction')->group(function () {
   Route::middleware(['auth:sanctum', 'check_user', 'check_role:supervisor'])->group(function () {
       Route::post('/', [TransactionController::class, 'create']);
       Route::get('/', [TransactionController::class, 'getTransactions']);
   }); 
});

Route::prefix('daily-balance')->group(function () {
   Route::middleware(['auth:sanctum', 'check_user', 'check_role:supervisor'])->group(function () {
        Route::get('/', [DailyBalanceController::class, 'getDailyBalances']);
        Route::get('/per-hours', [DailyBalanceController::class, 'getDailyBalancesPerHours']);
        Route::get('/per-weeks', [DailyBalanceController::class, 'getDailyBalancesPerWeeks']);
   }); 
});