<?php

declare(strict_types=1);

namespace Tests\Unit\v1\Rules\File;

use App\Rules\v1\File\ValidFilename;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    $this->rule = new ValidFilename;
    $this->fail = fn (string $message) => $this->message = $message;
});

it('validates files with correct names', function () {
    $validFiles = [
        'modlist.txt',
        'plugins.txt',
        'skyrim.ini',
        'fallout4.ini',
    ];

    foreach ($validFiles as $filename) {
        $file = UploadedFile::fake()->createWithContent($filename, 'test content');
        $this->rule->validate('file', $file, $this->fail);
        expect($this->message ?? null)->toBeNull();
    }
});

it('rejects files with invalid names', function () {
    $file = UploadedFile::fake()->createWithContent('invalid.txt', 'test content');
    $this->rule->validate('file', $file, $this->fail);
    expect($this->message)->toBe('The file is not named correctly.');
});

it('rejects non-file values', function () {
    $this->rule->validate('file', 'not a file', $this->fail);
    expect($this->message)->toBe('The :attribute must be a valid file upload.');
});

it('validates case-insensitive filenames', function () {
    $file = UploadedFile::fake()->createWithContent('MODLIST.TXT', 'test content');
    $this->rule->validate('file', $file, $this->fail);
    expect($this->message ?? null)->toBeNull();
});
