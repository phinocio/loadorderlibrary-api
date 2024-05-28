<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Check out the API docs at https://docs.loadorderlibrary.com']);
});
