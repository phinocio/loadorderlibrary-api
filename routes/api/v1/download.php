<?php

declare(strict_types=1);

use App\Http\Controllers\v1\File\FileDownloadController;
use Illuminate\Support\Facades\Route;

Route::controller(FileDownloadController::class)->group(function () {
    Route::get('/files/{name}/download', 'download')->name('files.download');
    Route::get('/lists/{load_order:slug}/download', 'downloadAllFiles')->name('lists.download');
});
