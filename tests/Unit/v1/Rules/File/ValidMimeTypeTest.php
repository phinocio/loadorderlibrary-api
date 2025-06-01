<?php

declare(strict_types=1);

namespace Tests\Unit\v1\Rules\File;

use App\Rules\v1\File\ValidMimeType;
use Illuminate\Http\UploadedFile;

test('validation passes for files with valid mime types', function () {
    $rule = new ValidMimeType;

    // Create a real text file for testing
    $testFile = tempnam(sys_get_temp_dir(), 'test');
    file_put_contents($testFile, 'test content');

    $file = new UploadedFile(
        $testFile,
        'test.txt',
        'text/plain',
        null,
        true
    );

    $fails = false;
    $rule->validate('test_file', $file, function () use (&$fails) {
        $fails = true;
    });

    expect($fails)->toBeFalse()
        ->and($file)->toBeInstanceOf(UploadedFile::class);

    unlink($testFile);
});

test('validation fails for files with invalid mime types', function () {
    $rule = new ValidMimeType;

    // Create a PDF file for testing
    $testFile = tempnam(sys_get_temp_dir(), 'test');
    file_put_contents($testFile, '%PDF-1.4'); // Add PDF header to ensure correct mime type

    $file = new UploadedFile(
        $testFile,
        'test.pdf',
        'application/pdf',
        null,
        true
    );

    $failMessage = '';
    $rule->validate('test_file', $file, function ($message) use (&$failMessage) {
        $failMessage = $message;
    });

    expect($failMessage)->toContain('invalid mimetype')
        ->and($failMessage)->toContain('application/pdf');

    unlink($testFile);
});
