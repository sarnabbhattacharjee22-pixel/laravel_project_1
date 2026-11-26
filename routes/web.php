<?php

use App\Http\Controllers\Backend\ProfileController as BackendProfileController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->prefix('/admin')->name('admin.')->group(function () {
    Route::get('/profile', [BackendProfileController::class, 'edit'])->name('profile.index');
    Route::post('/profile', [BackendProfileController::class, 'update'])->name('profile.update.own');
    Route::post('/info', [BackendProfileController::class, 'user_info_update'])->name('profile.update.info');
});

require __DIR__.'/auth.php';
