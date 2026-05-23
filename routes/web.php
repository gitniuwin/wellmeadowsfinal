<?php
use App\Http\Controllers\WardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\BillingController;

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

    // Module 3 — Ward & Bed Management
    Route::prefix('wards')->name('wards.')->group(function () {
        Route::get('/',             [WardController::class, 'index'])         ->name('index');
        Route::get('/create',       [WardController::class, 'create'])        ->name('create');
        Route::post('/',            [WardController::class, 'store'])         ->name('store');
        Route::get('/{ward}',       [WardController::class, 'show'])          ->name('show');
        Route::get('/{ward}/edit',  [WardController::class, 'edit'])          ->name('edit');
        Route::put('/{ward}',       [WardController::class, 'update'])        ->name('update');
        Route::delete('/{ward}',    [WardController::class, 'destroy'])       ->name('destroy');
        Route::post('/beds/assign',         [WardController::class, 'assignBed'])      ->name('beds.assign');
        Route::patch('/beds/{bed}/release', [WardController::class, 'releaseBed'])     ->name('beds.release');
        Route::patch('/beds/{bed}/status',  [WardController::class, 'updateBedStatus'])->name('beds.status');
    });
    
    // Module 5 — Billing & Reporting  ← ADD THIS BLOCK
        Route::get('/billing',         [BillingController::class, 'index'])->name('billing.index');
        Route::get('/billing/all',     [BillingController::class, 'allBills']);
        Route::get('/billing/create',  [BillingController::class, 'create']);
        Route::post('/billing',        [BillingController::class, 'store']);
        Route::delete('/billing/{id}', [BillingController::class, 'destroy']);
        Route::get('/payments',        [BillingController::class, 'payments']);
        Route::post('/payments',       [BillingController::class, 'recordPayment']);
        Route::get('/outstanding',     [BillingController::class, 'outstanding']);
        Route::get('/reports',         [BillingController::class, 'reports']);
});
