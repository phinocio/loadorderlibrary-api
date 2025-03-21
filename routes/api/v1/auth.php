<?php

use App\Http\Controllers\v1\Auth\LoginController;
use App\Http\Controllers\v1\Auth\LogoutController;
use App\Http\Controllers\v1\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    Route::post('/register', RegisterController::class)->name('auth.register');
    Route::post('/login', LoginController::class)->name('auth.login');
    Route::post('/logout', LogoutController::class)->name('auth.logout');
});

// test
Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', function () {
        return response()->json(['test' => 'meow']);
    });
});
