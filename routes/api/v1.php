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
        Route::delete('/lists/{load_order:slug}', [LoadOrdercontroller::class, 'destroy'])->name('list.destroy');
    });

    Route::get('/lists', [LoadOrderController::class, 'index'])->name('lists');
    Route::get('/lists/{load_order:slug}', [LoadOrderController::class, 'show'])->name('lists.show');
    Route::post('/lists', [LoadOrderController::class, 'store'])->name('lists.store');

    Route::get('/games', [GameController::class, 'index'])->name('games');
});
