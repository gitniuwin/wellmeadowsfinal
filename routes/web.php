<?php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

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
});

require __DIR__.'/auth.php';
