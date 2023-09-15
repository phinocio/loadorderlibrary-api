<?php

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
        Route::delete('/lists/{load_order:slug}', [LoadOrdercontroller::class, 'destroy'])->name('list.destroy')->middleware(['auth:sanctum', 'ability:delete']);

        /*
         * Game
         * Game related routes
         */
        Route::post('/games', [GameController::class, 'store'])->name('games.store');

        Route::post('/tokens/create', function (Request $request) {
            $token = $request->user()->createToken($request->token_name);

            return ['token' => $token->plainTextToken];
        });
    });

    // The following routes are usable by guests, so don't need sanctum middleware

    /*
     * List
     * List related routes
     */
    Route::controller(LoadOrderController::class)->group(function () {
        Route::get('/lists', 'index')->name('lists');
        Route::get('/lists/{load_order:slug}', 'show')->name('lists.show');
        Route::post('/lists', 'store')->name('lists.store');
    });

    /*
     * Game
     * Game related routes
     */
    Route::controller(LoadOrderController::class)->group(function () {
        Route::get('/games', 'index')->name('games');
        Route::get('/games/{game:name}', 'show')->name('games.show');
    });
});
