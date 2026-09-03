<?php

declare(strict_types=1);

use App\Http\Controllers\v1\Auth\NexusModsOAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('oauth')->group(function () {
    Route::middleware(['web', 'deny.authenticated'])->group(function () {
        Route::controller(NexusModsOAuthController::class)->group(function () {
            Route::get('nexus/authenticate', 'authenticate')->name('oauth.nexusmods.authenticate');
            Route::get('nexus/callback', 'callback')->name('oauth.nexusmods.callback');
        });
    });
});
