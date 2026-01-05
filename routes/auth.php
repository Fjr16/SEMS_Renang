<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticateController;

Route::middleware(['auth'])->group(function () {
    Route::delete('/logout', [AuthenticateController::class, 'destroy'])->name('logout');
});

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthenticateController::class, 'index'])->name('login');
    Route::post('/login', [AuthenticateController::class, 'store'])->name('login.store');
});

