<?php

declare(strict_types=1);

use App\Http\Controllers\v1\Admin\AdminUserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::prefix('users')->controller(AdminUserController::class)->group(function () {
        Route::get('/', 'index')->name('admin.users.index');
        Route::patch('/{user:name}', 'update')->name('admin.users.update');
        Route::delete('/{user:name}', 'destroy')->name('admin.users.destroy');
    });
});
