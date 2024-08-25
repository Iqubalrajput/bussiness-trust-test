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

Route::middleware(['middleware' => 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    Route::resource('admin/employees', AdminController::class, ['only' => ['index', 'create', 'store', 'edit', 'update', 'destroy']]);
    Route::resource('admin/leaves', AdminController::class, ['only' => ['index']]);
    Route::post('/admin/leaves/{id}/approve', [AdminController::class, 'approveLeave'])->name('admin.leaves.approve');
    Route::post('/admin/leaves/{id}/reject', [AdminController::class, 'rejectLeave'])->name('admin.leaves.reject');

    Route::resource('admin/advance-salaries', AdminController::class, ['only' => ['index']]);
    Route::post('/admin/advance-salaries/{id}/approve', [AdminController::class, 'approveAdvanceSalary'])->name('admin.advance-salaries.approve');
    Route::post('/admin/advance-salaries/{id}/reject', [AdminController::class, 'rejectAdvanceSalary'])->name('admin.advance-salaries.reject');

    Route::post('/admin/generate-monthly-salaries', [AdminController::class, 'generateMonthlySalaries'])->name('admin.generate.monthly.salaries');

});
// Employee Routes (Protected)
Route::group(['middleware' => 'employee'], function () {
    Route::get('/employee/dashboard', [EmployeeController::class, 'index'])->name('employee.dashboard');

    Route::get('/employee/profile', [EmployeeController::class, 'profile'])->name('employee.profile');

    Route::get('/employee/leaves', [EmployeeController::class, 'manageLeaves'])->name('employee.leaves');
    Route::post('/employee/leaves', [EmployeeController::class, 'applyLeave'])->name('employee.leaves.apply');

    Route::get('/employee/salary', [EmployeeController::class, 'salary'])->name('employee.salary');
    Route::get('/employee/advance-salary', [EmployeeController::class, 'advanceSalary'])->name('employee.advance.salary');
});
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
