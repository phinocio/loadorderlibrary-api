<?php

declare(strict_types=1);

use App\Http\Controllers\v1\Game\GameController;
use Illuminate\Support\Facades\Route;

Route::prefix('games')->group(function () {
    Route::controller(GameController::class)->group(function () {
        Route::get('/', 'index')->name('games.index');
        Route::get('/{slug}', 'show')->name('games.show');
    });
});
