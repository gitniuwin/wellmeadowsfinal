<?php
use App\Http\Controllers\PatientController;
use App\Http\Controllers\WardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\TreatmentController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\StaffAssignmentController;
use App\Http\Controllers\ScheduleController;

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
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');

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

    // Module 5 — Billing & Reporting
    Route::get('/billing',         [BillingController::class, 'index'])->name('billing.index');
    Route::get('/billing/all',     [BillingController::class, 'allBills'])->name('billing.all');
    Route::get('/billing/create',  [BillingController::class, 'create'])->name('billing.create');
    Route::post('/billing',        [BillingController::class, 'store'])->name('billing.store');
    Route::delete('/billing/{id}', [BillingController::class, 'destroy'])->name('billing.destroy');
    Route::get('/payments',        [BillingController::class, 'payments'])->name('payments.index');
    Route::post('/payments',       [BillingController::class, 'recordPayment'])->name('payments.store');
    Route::get('/outstanding',     [BillingController::class, 'outstanding'])->name('billing.outstanding');
    Route::get('/reports',         [BillingController::class, 'reports'])->name('billing.reports');

    // Module 4 — Appointment & Treatment Management
    Route::resource('appointments', AppointmentController::class)->only(['index', 'store', 'destroy']);
    Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status');
    Route::resource('treatments', TreatmentController::class)->only(['index', 'store', 'destroy']);
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/history/{patient}', [HistoryController::class, 'show'])->name('history.show');
    Route::resource('staff-assignment', StaffAssignmentController::class)
        ->only(['index', 'store', 'destroy']);
    
    // Module 1 — Patient Management
    Route::resource('patients', PatientController::class);
    Route::patch('/patients/{patient}/discharge', [PatientController::class, 'discharge'])->name('patients.discharge');
});
