<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth/login');
});

Route::get('/admin', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');
Route::get('/admin/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');
Route::get('/employee/login', [LoginController::class, 'showEmployeeLoginForm'])->name('employee.login');

Route::post('/admin/login', [LoginController::class, 'adminLogin'])->name('admin.login.submit');
Route::post('/employee/login', [LoginController::class, 'employeeLogin'])->name('employee.login.submit');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Auth::routes();

// Admin Logout Route
Route::post('/admin/logout', [LoginController::class, 'adminLogout'])->name('admin.logout');

// Employee Logout Route
Route::post('/employee/logout', [LoginController::class, 'employeeLogout'])->name('employee.logout');

Route::group(['middleware' => 'admin'], function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index']);
    // other admin routes
});
// Employee Routes (Protected)
Route::group(['middleware' => 'employee'], function () {
    Route::get('/employee/dashboard', [EmployeeController::class, 'index']);
    // other employee routes
});
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
