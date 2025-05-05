<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\v1\File\UploadFile;
use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

beforeEach(function () {
    Storage::fake('uploads');
    $this->action = new UploadFile;
});

it('uploads a file and returns the file model', function () {
    $content = "line1\r\nline2\r\nline3";
    $file = UploadedFile::fake()->createWithContent('test.txt', $content);

    $result = $this->action->execute($file);

    $normalizedContent = "line1\nline2\nline3";
    $expectedFileName = mb_strtolower(md5($file->getClientOriginalName().$normalizedContent).'-'.$file->getClientOriginalName());

    expect($result)->toBeInstanceOf(File::class)
        ->and($result->name)->toBe($expectedFileName);
    Storage::disk('uploads')->assertExists($result->name);
});

it('normalizes line endings in the file', function () {
    $content = "line1\r\nline2\r\nline3";
    $file = UploadedFile::fake()->createWithContent('test.txt', $content);

    $result = $this->action->execute($file);

    $storedContent = Storage::disk('uploads')->get($result->name);
    expect($storedContent)->not->toContain("\r\n");
    expect($storedContent)->toContain("\n");
});

it('returns same filename for identical content', function () {
    $content = "line1\nline2\nline3";
    $file1 = UploadedFile::fake()->createWithContent('test1.txt', $content);
    $file2 = UploadedFile::fake()->createWithContent('test1.txt', $content);

    $result1 = $this->action->execute($file1);
    $result2 = $this->action->execute($file2);

    expect($result1)->toBeInstanceOf(File::class)
        ->and($result2)->toBeInstanceOf(File::class)
        ->and($result1->name)->toBe($result2->name);
    Storage::disk('uploads')->assertExists($result1->name);
});

it('throws exception when file cannot be read', function () {
    $file = UploadedFile::fake()->create('test.txt', 0);
    chmod($file->path(), 0000); // Make file unreadable

    expect(fn () => $this->action->execute($file))
        ->toThrow(RuntimeException::class, 'Failed to read file contents');
});

it('creates a File model with correct attributes', function () {
    $content = "line1\r\nline2\r\nline3";
    $file = UploadedFile::fake()->createWithContent('test.txt', $content);

    $result = $this->action->execute($file);

    $normalizedContent = "line1\nline2\nline3";
    $expectedFileName = mb_strtolower(md5($file->getClientOriginalName().$normalizedContent).'-'.$file->getClientOriginalName());

    expect($result)->toBeInstanceOf(File::class)
        ->and($result->name)->toBe($expectedFileName)
        ->and($result->clean_name)->toBe('test.txt')
        ->and($result->size_in_bytes)->toBe(mb_strlen($content));

    $this->assertDatabaseHas('files', [
        'name' => $expectedFileName,
        'clean_name' => 'test.txt',
        'size_in_bytes' => mb_strlen($content),
    ]);
});
