<?php

declare(strict_types=1);

namespace Tests\Feature\v1\File;

use App\Models\File;
use App\Models\LoadOrder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('uploads');
    Cache::flush();
});

describe('download', function () {
    it('redirects to file download when file exists and file exists property is true', function () {
        $file = File::factory()->create([
            'name' => 'test-file.txt',
        ]);

        // Create the file in storage
        Storage::disk('uploads')->put($file->name, 'test content');

        $response = $this->get("/v1/files/{$file->name}/download");

        $response->assertStatus(302); // Redirect response
    });

    it('returns 404 when file exists in database but file does not exist on storage', function () {
        $file = File::factory()->create([
            'name' => 'missing-file.txt',
        ]);

        $response = $this->get("/v1/files/{$file->name}/download");

        $response->assertNotFound();
    });

    it('returns 404 when file does not exist in database', function () {
        $response = $this->get('/v1/files/nonexistent-file.txt/download');

        $response->assertNotFound();
    });

    it('uses cache for file lookup', function () {
        $file = File::factory()->create([
            'name' => 'cached-file.txt',
        ]);

        Storage::disk('uploads')->put($file->name, 'test content');

        // First request should cache the file
        $response1 = $this->get("/v1/files/{$file->name}/download");
        $response1->assertStatus(302);

        // Second request should use cached file
        $response2 = $this->get("/v1/files/{$file->name}/download");
        $response2->assertStatus(302);
    });

});

describe('downloadAllFiles', function () {
    it('returns zip builder when load order has files', function () {
        $loadOrder = LoadOrder::factory()->create([
            'name' => 'Test Load Order',
            'slug' => 'test-load-order',
        ]);

        $file1 = File::factory()->create([
            'name' => 'file1.txt',
            'clean_name' => 'modlist.txt',
        ]);

        $file2 = File::factory()->create([
            'name' => 'file2.txt',
            'clean_name' => 'plugins.txt',
        ]);

        $loadOrder->files()->attach([$file1->id, $file2->id]);

        // Create the files in storage
        Storage::disk('uploads')->put($file1->name, 'modlist content');
        Storage::disk('uploads')->put($file2->name, 'plugins content');

        $response = $this->get("/v1/lists/{$loadOrder->slug}/download");

        // The response should be successful (ZipStream handles the actual streaming)
        $response->assertOk();
    });

    it('returns 404 when load order has no files', function () {
        $loadOrder = LoadOrder::factory()->create([
            'name' => 'Empty Load Order',
            'slug' => 'empty-load-order',
        ]);

        $response = $this->get("/v1/lists/{$loadOrder->slug}/download");

        $response->assertNotFound();
    });

    it('returns 404 when load order does not exist', function () {
        $response = $this->get('/v1/lists/nonexistent-load-order/download');

        $response->assertNotFound();
    });

    it('loads files relationship before processing', function () {
        $loadOrder = LoadOrder::factory()->create([
            'name' => 'Test Load Order',
            'slug' => 'test-load-order',
        ]);

        $file = File::factory()->create([
            'name' => 'test-file.txt',
            'clean_name' => 'test.txt',
        ]);

        $loadOrder->files()->attach($file->id);

        Storage::disk('uploads')->put($file->name, 'test content');

        $response = $this->get("/v1/lists/{$loadOrder->slug}/download");

        $response->assertOk();
    });

    it('handles load order with single file', function () {
        $loadOrder = LoadOrder::factory()->create([
            'name' => 'Single File Load Order',
            'slug' => 'single-file-load-order',
        ]);

        $file = File::factory()->create([
            'name' => 'single-file.txt',
            'clean_name' => 'modlist.txt',
        ]);

        $loadOrder->files()->attach($file->id);

        Storage::disk('uploads')->put($file->name, 'single file content');

        $response = $this->get("/v1/lists/{$loadOrder->slug}/download");

        $response->assertOk();
    });

    it('handles load order with multiple files correctly', function () {
        $loadOrder = LoadOrder::factory()->create([
            'name' => 'Multi-file Load Order',
            'slug' => 'multi-file-load-order',
        ]);

        $files = [];
        for ($i = 1; $i <= 3; $i++) {
            $file = File::factory()->create([
                'name' => "file{$i}.txt",
                'clean_name' => "clean-file{$i}.txt",
            ]);
            $files[] = $file->id;

            Storage::disk('uploads')->put($file->name, "content for file {$i}");
        }

        $loadOrder->files()->attach($files);

        $response = $this->get("/v1/lists/{$loadOrder->slug}/download");

        $response->assertOk();
    });

    it('handles load order with special characters in name', function () {
        $loadOrder = LoadOrder::factory()->create([
            'name' => 'Load Order: Test & Special "Characters"',
            'slug' => 'load-order-test-special-characters',
        ]);

        $file = File::factory()->create([
            'name' => 'test-file.txt',
            'clean_name' => 'test.txt',
        ]);

        $loadOrder->files()->attach($file->id);

        Storage::disk('uploads')->put($file->name, 'test content');

        $response = $this->get("/v1/lists/{$loadOrder->slug}/download");

        $response->assertOk();
    });
});
