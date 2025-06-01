<?php

declare(strict_types=1);

use App\Models\File;
use App\Models\LoadOrder;
use Illuminate\Support\Facades\Storage;

it('deletes orphaned files', function () {
    // Mock the Storage facade
    Storage::fake('uploads');

    // Create files that are attached to load orders (not orphaned)
    $loadOrder = LoadOrder::factory()->create();
    $attachedFiles = File::factory()->count(2)->create();
    $loadOrder->files()->attach($attachedFiles);

    // Create orphaned files (not attached to any load order)
    $orphanedFiles = File::factory()->count(3)->create();

    // Execute the command
    $this->artisan('lists:delete-orphaned')
        ->expectsOutput('3 orphaned files deleted.')
        ->assertSuccessful();

    // Assert orphaned files are deleted
    foreach ($orphanedFiles as $file) {
        $this->assertDatabaseMissing('files', [
            'id' => $file->id,
        ]);
        Storage::disk('uploads')->assertMissing($file->name);
    }

    // Assert non-orphaned files remain
    foreach ($attachedFiles as $file) {
        $this->assertDatabaseHas('files', [
            'id' => $file->id,
        ]);
    }
});

it('handles no orphaned files gracefully', function () {
    // Mock the Storage facade
    Storage::fake('uploads');

    // Create files that are attached to load orders (not orphaned)
    $loadOrder = LoadOrder::factory()->create();
    $attachedFiles = File::factory()->count(2)->create();
    $loadOrder->files()->attach($attachedFiles);

    // Execute the command
    $this->artisan('lists:delete-orphaned')
        ->expectsOutput('0 orphaned files deleted.')
        ->assertSuccessful();

    // Verify the database still contains the same number of records
    $this->assertDatabaseCount('files', 2);
});
