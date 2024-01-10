<?php

use App\Http\Controllers\Api\v1\FileController;
use App\Http\Controllers\Api\v1\GameController;
use App\Http\Controllers\Api\v1\LoadOrderController;
use App\Http\Controllers\Api\v1\UserController;
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
        Route::get('/user', [UserController::class, 'show'])->name('user.show');
        Route::get('/user/lists', [UserController::class, 'lists'])->name('user.lists');

        /*
         * Passing an instance of a resource to the controller for deletion is
         * convention of other resources. In addition, this will allow an
         * admin to delete any user they choose by passing a name.
         */
        Route::delete('/user/{user:name}', [UserController::class, 'destroy'])->name('user.destroy');

        /*
         * List
         * List related routes
         */
        Route::controller(LoadOrderController::class)->group(function () {
            Route::put('/lists/{load_order:slug}', 'update')
                ->name('list.update')
                ->middleware(['ability:update']); //TODO: Check that simply defining the ability here works since it's already in a sanctum middleware group

            Route::delete('/lists/{load_order:slug}', 'destroy')
                ->name('list.destroy')
                ->middleware(['auth:sanctum', 'ability:delete']);
        });


        /*
         * Game
         * Game related routes
         */
        Route::post('/games', [GameController::class, 'store'])->name('games.store');
    });

    // The following routes are usable by guests, so don't need sanctum middleware

    /*
     * List
     * List related routes
     */
    Route::controller(LoadOrderController::class)->group(function () {
        Route::get('/lists', 'index')->name('list');
        Route::get('/lists/{load_order:slug}', 'show')->name('list.show');
        Route::post('/lists', 'store')->name('list.store');
    });

    /*
     * Game
     * Game related routes
     */
    Route::controller(GameController::class)->group(function () {
        Route::get('/games', 'index')->name('games');
        Route::get('/games/{game:name}', 'show')->name('games.show');
    });

    /*
     * File
     * File related routes
     */
    Route::controller(FileController::class)->group(function () {
        Route::get('/lists/{load_order:slug}/download', 'index')->name('files');
        Route::get('/lists/{load_order:slug}/download/{file:name}', 'show')->name('files.show');
        Route::get('/lists/{load_order:slug}/embed/{file:name}', 'embed')->name('files.embed');
    });
});
