<?php

use App\Http\Controllers\GameController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoadOrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/lists', [LoadOrderController::class, 'index'])->name('lists');
Route::get('/lists/{load_order:slug}', [LoadOrderController::class, 'show'])->name('lists.show');
Route::post('/lists', [LoadOrderController::class, 'store'])->name('lists.store');
Route::delete('/lists/{load_order:slug}', [LoadOrdercontroller::class, 'destroy'])->name('list.destroy');

Route::get('/games', [GameController::class, 'index'])->name('games');

Route::get('/compare', [ComparisonController::class, 'show'])->name('compare.show');