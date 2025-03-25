<?php

declare(strict_types=1);

use App\Http\Controllers\v1\User\UserController;
use App\Http\Controllers\v1\User\UserEmailController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->middleware('auth:sanctum')->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::get('/', 'index')->name('users.index');
        Route::get('/{user:name}', 'show')->name('users.show');
        Route::delete('/{user:name}', 'destroy')->name('users.destroy');
    });

    Route::controller(UserEmailController::class)->group(function () {
        Route::patch('/{user:name}/email', 'update')->name('users.email.update');
        Route::delete('/{user:name}/email', 'destroy')->name('users.email.destroy');
    });
});
