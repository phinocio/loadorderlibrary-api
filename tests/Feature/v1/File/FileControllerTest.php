<?php

declare(strict_types=1);

namespace Tests\Feature\v1\File;

use App\Models\File;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('uploads');
    $this->content = "line1\nline2\nline3";
    $this->file = File::factory()->create(['name' => 'test.txt']);
    Storage::disk('uploads')->put($this->file->name, $this->content);
});

describe('show', function () {
    it('returns file with content', function () {
        $response = $this->getJson("/v1/files/{$this->file->name}");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'name',
                    'clean_name',
                    'size_in_bytes',
                    'content',
                ],
            ])
            ->assertJsonPath('data.name', $this->file->name)
            ->assertJsonPath('data.content', explode("\n", $this->content));
    });

    it('returns 404 when file does not exist', function () {
        $this->getJson('/v1/files/nonexistent.txt')->assertNotFound();
    });
});
