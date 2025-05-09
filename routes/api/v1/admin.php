<?php

declare(strict_types=1);

use App\Http\Controllers\v1\Admin\AdminGameController;
use App\Http\Controllers\v1\Admin\AdminLoadOrderController;
use App\Http\Controllers\v1\Admin\AdminUserController;
use App\Http\Controllers\v1\Admin\AdminUserPasswordController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::prefix('users')->group(function () {
        Route::controller(AdminUserController::class)->group(function () {
            Route::get('/', 'index')->name('admin.users.index');
            Route::get('/{user:name}', 'show')->name('admin.users.show');
            Route::patch('/{user:name}', 'update')->name('admin.users.update');
            Route::delete('/{user:name}', 'destroy')->name('admin.users.destroy');
        });

        Route::controller(AdminUserPasswordController::class)->group(function () {
            Route::patch('/{user:name}/password', 'update')->name('admin.users.password.update');
        });
    });

    Route::prefix('games')->group(function () {
        Route::controller(AdminGameController::class)->group(function () {
            Route::post('/', 'store')->name('admin.games.store');
        });
    });

    Route::prefix('lists')->group(function () {
        Route::controller(AdminLoadOrderController::class)->group(function () {
            Route::delete('/{slug}', 'destroy')->name('admin.lists.destroy');
        });
    });
});
