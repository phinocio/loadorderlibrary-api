<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    require __DIR__.'/v1/auth.php';
    require __DIR__.'/v1/admin.php';
    require __DIR__.'/v1/user.php';
    require __DIR__.'/v1/game.php';
});
