<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\AdvanceSalaryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::get('test',function(){
    return 'hi Developer. this tesing api.';
});

// employee portal 
Route::prefix('employee')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
    });

    Route::middleware(['auth:sanctum', 'role:employee'])->group(function () {
        Route::prefix('auth')->group(function () {
            Route::get('/profile', [AuthController::class, 'checkProfileDetails']);
            Route::put('/update', [AuthController::class, 'update']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });
        Route::get('/leaves/{user_id}', [LeaveController::class, 'getEmployeeLeaves']);
        Route::post('/leaves', [LeaveController::class, 'store']);
        Route::put('/leaves/{id}', [LeaveController::class, 'update']);
        Route::delete('/leaves/{id}', [LeaveController::class, 'destroy']);
        Route::get('/profile', [LeaveController::class, 'showProfile']);
        Route::get('/salaries', [SalaryController::class, 'index']);
        Route::get('/salaries/pdf', [SalaryController::class, 'generatePDF']);
        Route::post('/advance-salaries', [AdvanceSalaryController::class, 'store']);
        Route::put('/advance-salaries/{id}', [AdvanceSalaryController::class, 'update']);
        Route::delete('/advance-salaries/{id}', [AdvanceSalaryController::class, 'destroy']);
        });
});



// admin login 
Route::prefix('admin')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AdminController::class, 'login']);
    });

    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::prefix('auth')->group(function () {
            Route::put('/update', [AuthController::class, 'update']);
            Route::post('/logout', [AdminController::class, 'logout']);
        });
    });
});



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
