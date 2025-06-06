<?php

declare(strict_types=1);

use App\Http\Controllers\v1\Auth\ApiTokenController;
use App\Http\Controllers\v1\Auth\CurrentUserController;
use App\Http\Controllers\v1\Auth\ForgotPasswordController;
use App\Http\Controllers\v1\Auth\LoginController;
use App\Http\Controllers\v1\Auth\LogoutController;
use App\Http\Controllers\v1\Auth\RegisterController;
use App\Http\Controllers\v1\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    Route::middleware('deny.authenticated')->group(function () {
        Route::post('/register', RegisterController::class)->name('auth.register');
        Route::post('/login', LoginController::class)->name('auth.login');
        Route::post('/forgot-password', ForgotPasswordController::class)->name('auth.forgot-password');
        Route::post('/reset-password', ResetPasswordController::class)->name('auth.reset-password');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', LogoutController::class)->name('auth.logout');

        Route::get('/api-tokens', [ApiTokenController::class, 'index'])->name('auth.api-tokens.index');
        Route::post('/api-tokens', [ApiTokenController::class, 'store'])->name('auth.api-tokens.store');
        Route::delete('/api-tokens/{token}', [ApiTokenController::class, 'destroy'])->name('auth.api-tokens.destroy');
    });
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/me', CurrentUserController::class)->middleware('abilities:read')->name('auth.me');
});
