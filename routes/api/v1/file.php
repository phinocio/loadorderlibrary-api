<?php

declare(strict_types=1);

use App\Actions\v1\File\DownloadFile;
use App\Http\Controllers\v1\File\FileController;
use App\Models\File;
use Illuminate\Support\Facades\Route;

Route::prefix('/files')->group(function () {
    Route::get('/{file:name}/download', function (File $file, DownloadFile $downloadFile) {
        return $downloadFile->execute($file);
    })->name('files.download');

    Route::controller(FileController::class)->group(function () {
        Route::get('/{file:name}', 'show')->name('files.show');
        Route::delete('/{file:name}', 'destroy')->name('files.destroy');
    });
});
