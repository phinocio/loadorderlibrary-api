<?php

declare(strict_types=1);

namespace Tests\Feature\v1\File;

use App\Models\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('uploads');
    // Clear the cache to prevent test interference
    Cache::flush();
    $this->content = "line1\nline2\nline3";
    $this->file = File::factory()->create(['name' => 'test.txt', 'clean_name' => 'test.txt']);
    Storage::disk('uploads')->put($this->file->name, $this->content);
});

describe('show', function () {
    it('returns regular file content in original order', function () {
        $response = $this->getJson("/v1/files/{$this->file->name}");

        $expectedContent = explode("\n", $this->content);
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
            ->assertJsonPath('data.content', $expectedContent, 'File content should be returned in original order for non-modlist files');
    });

    it('returns modlist file content in reverse order', function () {
        $modlistFile = File::factory()->create(['name' => 'modlist.txt', 'clean_name' => 'modlist.txt']);
        Storage::disk('uploads')->put($modlistFile->name, $this->content);

        $response = $this->getJson("/v1/files/{$modlistFile->name}");

        $expectedContent = array_reverse(explode("\n", $this->content));
        $response->assertOk()
            ->assertJsonPath('data.name', $modlistFile->name)
            ->assertJsonPath('data.content', $expectedContent, 'Modlist.txt content should be returned in reverse order');
    });

    it('returns 404 when file does not exist', function () {
        $this->getJson('/v1/files/nonexistent.txt')->assertNotFound();
    });
});
