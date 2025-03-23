<?php

declare(strict_types=1);

use App\Http\Controllers\v1\Auth\CurrentUserController;
use App\Http\Controllers\v1\Auth\LoginController;
use App\Http\Controllers\v1\Auth\LogoutController;
use App\Http\Controllers\v1\Auth\RegisterController;
use App\Http\Resources\v1\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    Route::middleware('deny.authenticated')->group(function () {
        Route::post('/register', RegisterController::class)->name('auth.register');
        Route::post('/login', LoginController::class)->name('auth.login');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/me', CurrentUserController::class)->name('auth.me');
        Route::post('/logout', LogoutController::class)->name('auth.logout');
    });
});

// test
Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', function () {
        return new UserResource(Auth::user());
    });
});
