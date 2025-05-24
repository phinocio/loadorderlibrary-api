<?php

declare(strict_types=1);

use App\Http\Controllers\v1\File\FileDownloadController;
use Illuminate\Support\Facades\Route;

Route::controller(FileDownloadController::class)->group(function () {
    // Download individual file
    Route::get('/files/{name}/download', 'download')->name('files.download');

    // Download all files from a load order as zip
    Route::get('/lists/{slug}/download', 'downloadAllFiles')->name('lists.download');
});
