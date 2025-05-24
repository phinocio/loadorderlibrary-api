<?php

declare(strict_types=1);

use App\Http\Controllers\v1\File\FileController;
use Illuminate\Support\Facades\Route;

Route::prefix('/files')->group(function () {
    Route::controller(FileController::class)->group(function () {
        Route::get('/{name}', 'show')->name('files.show');
    });
});
