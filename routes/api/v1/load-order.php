<?php

declare(strict_types=1);

use App\Http\Controllers\v1\LoadOrder\LoadOrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('lists')->group(function () {
    Route::controller(LoadOrderController::class)->group(function () {
        Route::get('/', 'index')->name('lists.index');
        Route::post('/', 'store')->name('lists.store');
        Route::get('/{slug}', 'show')->name('lists.show');

        Route::middleware('auth:sanctum')->group(function () {
            Route::patch('/{load_order:slug}', 'update')->name('lists.update');
            Route::delete('/{slug}', 'destroy')->name('lists.destroy');
        });
    });
});
