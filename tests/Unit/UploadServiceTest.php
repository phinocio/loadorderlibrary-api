<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Storage;
use App\Services\UploadService;

class UploadServiceTest extends TestCase
{
	/** @test */
	public function files_get_uploaded()
	{
		$files = [
			UploadedFile::fake()->create('modlist.txt', 1),
			UploadedFile::fake()->create('plugins.txt', 4),
		];

		$files = UploadService::uploadFiles($files);

		Storage::disk('uploads')
			->assertExists(explode(',', $files));

		// Clean up the files
		Storage::disk('uploads')->delete(explode(',', $files));
	}
}
