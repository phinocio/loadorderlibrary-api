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
			UploadedFile::fake()->create('modlist.txt', 1, 'text/plain'),
			UploadedFile::fake()->create('plugins.txt', 4, 'text/plain'),
		];

		$files = UploadService::uploadFiles($files);

		Storage::disk('uploads')
			->assertExists($files[0]['name']);

		// Clean up the files
		Storage::disk('uploads')->delete($files[0]['name']);
	}
}
