<?php

declare(strict_types=1);

use App\Http\Controllers\v1\User\UserController;
use App\Http\Controllers\v1\User\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->middleware('auth:sanctum')->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::get('/', 'index')->name('users.index');
        Route::get('/{user:name}', 'show')->name('users.show');
        Route::patch('/{user:name}', 'update')->name('users.update');
        Route::delete('/{user:name}', 'destroy')->name('users.destroy');
    });

    Route::controller(UserProfileController::class)->group(function () {
        Route::patch('/{user:name}/profile', 'update')->name('users.profile.update');
    });
});
