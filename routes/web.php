<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StaffController;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected routes (must be logged in)
Route::middleware('auth')->group(function () {

    // Dashboard — all roles
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Staff — all roles can view
    Route::get('/staff',           [StaffController::class, 'index'])->name('staff.index');
    Route::get('/staff/search',    [StaffController::class, 'search'])->name('staff.search');
    Route::get('/staff/{id}/edit', [StaffController::class, 'edit'])->name('staff.edit');

    // Staff — Medical Director & HR only can write
    Route::middleware('role:Medical Director,Personnel/HR Staff')->group(function () {
        Route::post('/staff',                      [StaffController::class, 'store'])->name('staff.store');
        Route::put('/staff/{id}',                  [StaffController::class, 'update'])->name('staff.update');
        Route::delete('/staff/{id}',               [StaffController::class, 'destroy'])->name('staff.destroy');
        Route::put('/staff/{id}/schedule',         [StaffController::class, 'updateSchedule'])->name('staff.schedule');
        Route::put('/staff/{id}/responsibilities', [StaffController::class, 'updateResponsibilities'])->name('staff.responsibilities');
    });
});