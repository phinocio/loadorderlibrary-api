<?php

declare(strict_types=1);

namespace Tests\Unit\v1\Actions\File;

use App\Actions\v1\File\DownloadAllFiles;
use App\Models\File;
use App\Models\LoadOrder;
use Illuminate\Support\Facades\Storage;
use Mockery;
use STS\ZipStream\Builder;
use STS\ZipStream\Facades\Zip;

beforeEach(function () {
    $this->action = new DownloadAllFiles;
});

it('creates a zip file with all files from a load order', function () {
    // Create a load order with files
    $loadOrder = LoadOrder::factory()->create([
        'name' => 'Test Load Order',
    ]);

    $file1 = File::factory()->create([
        'name' => 'test-file-1.txt',
        'clean_name' => 'modlist.txt',
    ]);

    $file2 = File::factory()->create([
        'name' => 'test-file-2.txt',
        'clean_name' => 'plugins.txt',
    ]);

    $loadOrder->files()->attach([$file1->id, $file2->id]);
    $loadOrder = $loadOrder->fresh(['files']);

    // Mock Storage facade
    $mockDisk = Mockery::mock();
    $mockDisk->shouldReceive('temporaryUrl')
        ->with('test-file-1.txt', Mockery::type('DateTimeInterface'))
        ->once()
        ->andReturn('https://test-bucket.s3.amazonaws.com/test-file-1.txt');

    $mockDisk->shouldReceive('temporaryUrl')
        ->with('test-file-2.txt', Mockery::type('DateTimeInterface'))
        ->once()
        ->andReturn('https://test-bucket.s3.amazonaws.com/test-file-2.txt');

    Storage::shouldReceive('disk')
        ->with('uploads')
        ->twice()
        ->andReturn($mockDisk);

    // Mock the Zip facade
    $mockZip = Mockery::mock(Builder::class);
    $mockZip->shouldReceive('add')
        ->with('https://test-bucket.s3.amazonaws.com/test-file-1.txt', 'modlist.txt')
        ->once()
        ->andReturnSelf();

    $mockZip->shouldReceive('add')
        ->with('https://test-bucket.s3.amazonaws.com/test-file-2.txt', 'plugins.txt')
        ->once()
        ->andReturnSelf();

    Zip::shouldReceive('create')
        ->with('Test Load Order.zip')
        ->once()
        ->andReturn($mockZip);

    $result = $this->action->execute($loadOrder);

    expect($result)->toBeInstanceOf(Builder::class);
});

it('creates empty zip when load order has no files', function () {
    $loadOrder = LoadOrder::factory()->create([
        'name' => 'Empty Load Order',
    ]);

    // Mock the Zip facade for empty load order
    $mockZip = Mockery::mock(Builder::class);

    Zip::shouldReceive('create')
        ->with('Empty Load Order.zip')
        ->once()
        ->andReturn($mockZip);

    $result = $this->action->execute($loadOrder);

    expect($result)->toBeInstanceOf(Builder::class);
});

it('generates temporary URLs with correct expiration time', function () {
    $loadOrder = LoadOrder::factory()->create([
        'name' => 'Test Load Order',
    ]);

    $file = File::factory()->create([
        'name' => 'test-file.txt',
        'clean_name' => 'test.txt',
    ]);

    $loadOrder->files()->attach($file->id);
    $loadOrder = $loadOrder->fresh(['files']);

    // Mock Storage with specific expiration check
    $mockDisk = Mockery::mock();
    $mockDisk->shouldReceive('temporaryUrl')
        ->with('test-file.txt', Mockery::on(function ($carbon) {
            // Check that the expiration is approximately 5 minutes from now
            $expectedTime = now()->addMinutes(5);

            return abs($carbon->diffInSeconds($expectedTime)) <= 1;
        }))
        ->once()
        ->andReturn('https://test-bucket.s3.amazonaws.com/test-file.txt');

    Storage::shouldReceive('disk')
        ->with('uploads')
        ->once()
        ->andReturn($mockDisk);

    // Mock the Zip facade
    $mockZip = Mockery::mock(Builder::class);
    $mockZip->shouldReceive('add')
        ->once()
        ->andReturnSelf();

    Zip::shouldReceive('create')
        ->once()
        ->andReturn($mockZip);

    $this->action->execute($loadOrder);
});

it('uses clean file names in the zip archive', function () {
    $loadOrder = LoadOrder::factory()->create([
        'name' => 'Test Load Order',
    ]);

    $file = File::factory()->create([
        'name' => 'hash-123-original-filename.txt',
        'clean_name' => 'original-filename.txt',
    ]);

    $loadOrder->files()->attach($file->id);
    $loadOrder = $loadOrder->fresh(['files']);

    // Mock Storage facade
    $mockDisk = Mockery::mock();
    $mockDisk->shouldReceive('temporaryUrl')
        ->once()
        ->andReturn('https://test-bucket.s3.amazonaws.com/hash-123-original-filename.txt');

    Storage::shouldReceive('disk')
        ->with('uploads')
        ->once()
        ->andReturn($mockDisk);

    // Mock the Zip facade and verify clean name is used
    $mockZip = Mockery::mock(Builder::class);
    $mockZip->shouldReceive('add')
        ->with('https://test-bucket.s3.amazonaws.com/hash-123-original-filename.txt', 'original-filename.txt')
        ->once()
        ->andReturnSelf();

    Zip::shouldReceive('create')
        ->once()
        ->andReturn($mockZip);

    $result = $this->action->execute($loadOrder);

    expect($result)->toBeInstanceOf(Builder::class);
});

it('handles load order names with special characters in zip filename', function () {
    $loadOrder = LoadOrder::factory()->create([
        'name' => 'Load Order: Test & Special "Characters"',
    ]);

    $file = File::factory()->create([
        'name' => 'test-file.txt',
        'clean_name' => 'test.txt',
    ]);

    $loadOrder->files()->attach($file->id);
    $loadOrder = $loadOrder->fresh(['files']);

    // Mock Storage facade
    $mockDisk = Mockery::mock();
    $mockDisk->shouldReceive('temporaryUrl')
        ->once()
        ->andReturn('https://test-bucket.s3.amazonaws.com/test-file.txt');

    Storage::shouldReceive('disk')
        ->with('uploads')
        ->once()
        ->andReturn($mockDisk);

    // Mock the Zip facade - zip filename should include special characters
    $mockZip = Mockery::mock(Builder::class);
    $mockZip->shouldReceive('add')
        ->once()
        ->andReturnSelf();

    Zip::shouldReceive('create')
        ->with('Load Order: Test & Special "Characters".zip')
        ->once()
        ->andReturn($mockZip);

    $result = $this->action->execute($loadOrder);

    expect($result)->toBeInstanceOf(Builder::class);
});
