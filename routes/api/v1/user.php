<?php

declare(strict_types=1);

use App\Http\Controllers\v1\User\UserController;
use App\Http\Controllers\v1\User\UserPasswordController;
use App\Http\Controllers\v1\User\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->group(function () {
    Route::controller(UserController::class)->middleware('auth')->group(function () {
        Route::patch('/{user:name}', 'update')->name('users.update');
        Route::delete('/{user:name}', 'destroy')->name('users.destroy');
    });

    Route::controller(UserPasswordController::class)->middleware('auth')->group(function () {
        Route::patch('/{user:name}/password', 'update')->name('users.password.update');
    });

    Route::controller(UserProfileController::class)->group(function () {
        // PUBLIC user profile
        Route::get('/{name}/profile', 'show')->name('users.profile.show');

        Route::middleware('auth')->group(function () {
            Route::patch('/{user:name}/profile', 'update')->name('users.profile.update');
        });
    });
});
