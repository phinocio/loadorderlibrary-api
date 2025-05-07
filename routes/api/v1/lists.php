<?php

declare(strict_types=1);

use App\Http\Controllers\v1\LoadOrder\LoadOrderController;

Route::prefix('lists')->group(function () {
    Route::controller(LoadOrderController::class)->group(function () {
        Route::get('/', 'index')->name('lists.index');
    });
});
