<?php

declare(strict_types=1);

namespace Tests\Unit\v1\Actions;

use App\Actions\v1\File\DeleteFile;
use App\Models\File;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('uploads');
    $this->action = new DeleteFile;
});

it('deletes file from storage and database', function () {
    $file = File::factory()->create();
    Storage::disk('uploads')->put($file->name, 'test content');

    $this->action->execute($file);

    Storage::disk('uploads')->assertMissing($file->name);
    $this->assertModelMissing($file);
});

it('handles non-existent file in storage gracefully', function () {
    $file = File::factory()->create();
    // Don't create the file in storage, only in database

    $this->action->execute($file);

    $this->assertModelMissing($file);
});
