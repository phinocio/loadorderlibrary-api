<?php

use App\Http\Controllers\Api\v1\Admin\GameController as AdminGameController;
use App\Http\Controllers\Api\v1\Admin\LoadOrderController as AdminLoadOrderController;
use App\Http\Controllers\Api\v1\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\v1\ComparisonController;
use App\Http\Controllers\Api\v1\FileController;
use App\Http\Controllers\Api\v1\GameController;
use App\Http\Controllers\Api\v1\LoadOrderController;
use App\Http\Controllers\Api\v1\StatsController;
use App\Http\Controllers\Api\v1\TokenController;
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::controller(UserController::class)->group(function () {
            Route::get('/user', 'show')->name('user.show');
            Route::get('/user/lists', 'lists')->name('user.lists');
        });

        Route::controller(LoadOrderController::class)->group(function () {
            Route::put('/lists/{load_order:slug}', 'update')
                ->name('list.update');
            Route::delete('/lists/{load_order:slug}', 'destroy')
                ->name('list.destroy');
        });
    });

    // Routes that require auth, but don't want to allow token auth with sanctum.
    Route::middleware('auth')->group(function () {
        Route::controller(TokenController::class)->group(function () {
            Route::get('/user/api-tokens', 'index')->name('token.index');
            Route::post('/user/api-tokens', 'store')->name('token.store');
            Route::delete('/user/api-tokens/{id}', 'destroy')->name('token.destroy');
        });

        /**
         * Passing an instance of a resource to the controller for deletion is
         * convention of other resources. In addition, this will allow an
         * admin to delete any user they choose by passing a name.
         * Being in the auth middleware vs auth:sanctum also means accounts can only
         * be deleted from the website itself.
         */
        Route::delete('/user/{user:name}', [UserController::class, 'destroy'])->name('user.destroy');

        /**
         * Admin Routes
         * Admin related routes for managing things with the site.
         */
        Route::prefix('admin')->middleware(EnsureUserIsAdmin::class)->group(function () {
            Route::controller(AdminUserController::class)->group(function () {
                Route::get('/users', 'index')->name('admin.user.index');
                Route::get('/users/{user:name}', 'show')->name('admin.user.show');
                Route::put('/users/{user:name}', 'update')->name('admin.user.update');
                Route::delete('/users/{user:name}', 'destroy')->name('admin.user.destroy');
            });

            Route::controller(AdminLoadOrderController::class)->group(function () {
                Route::get('/lists', 'index')->name('admin.lists.index');
                Route::get('/lists/{load_order:slug}', 'show')->name('admin.lists.show');
                Route::delete('/lists/{load_order:slug}', 'destroy')->name('admin.lists.destroy');
            });

            Route::controller(AdminGameController::class)->group(function () {
                Route::post('/games', 'store')->name('admin.games.store');
                Route::delete('/games/{game:name}', 'destroy')->name('admin.games.destroy');
            });
        });
    });

    /**
     * GUEST ROUTES
     * The following routes are usable by guests/anonymous users, so don't need sanctum auth.
     */
    Route::controller(LoadOrderController::class)->group(function () {
        Route::get('/lists', 'index')->name('list.index');
        Route::get('/lists/{load_order:slug}', 'show')->name('list.show');
        Route::post('/lists', 'store')->name('list.store');
    });

    Route::controller(GameController::class)->group(function () {
        Route::get('/games', 'index')->name('games.index');
        Route::get('/games/{game:name}', 'show')->name('games.show');
    });

    Route::controller(FileController::class)->group(function () {
        Route::get('/lists/{load_order:slug}/download', 'index')->name('files.index');
        Route::get('/lists/{load_order:slug}/download/{file:name}', 'show')->name('files.show');
        Route::get('/lists/{load_order:slug}/embed/{file:name}', 'embed')->name('files.embed');
    });

    Route::controller(ComparisonController::class)->group(function () {
        Route::get('/compare', 'index')->name('compare.index');
        Route::get('/compare/{load_order1}/{load_order2}', 'show')->name('compare.show');
    });

    Route::controller(StatsController::class)->group(function () {
        Route::get('/stats', 'index')->name('stats.index');
        Route::get('/stats/{resource}', 'show')->name('stats.show');
    });
});
