<?php

declare(strict_types=1);

namespace Tests\Unit\v1\Actions;

use App\Actions\v1\File\GetFileContent;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

beforeEach(function () {
    Storage::fake('uploads');
    $this->getFileContent = new GetFileContent;
    $this->file = File::factory()->create(['name' => 'test.txt']);
    $this->content = "line1\nline2\nline3";
});

it('returns file content as array of lines', function () {
    // Arrange
    Storage::disk('uploads')->put($this->file->name, $this->content);
    $expectedLines = ['line1', 'line2', 'line3'];

    // Act
    $result = $this->getFileContent->execute($this->file);

    // Assert
    expect($result)->toBe($expectedLines);
});

it('throws exception when file cannot be read', function () {
    // Arrange
    Storage::disk('uploads')->delete($this->file->name);

    // Act & Assert
    $this->getFileContent->execute($this->file);
})->throws(RuntimeException::class, 'Failed to read file contents');
