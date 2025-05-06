<?php

declare(strict_types=1);

namespace Tests\Unit\v1\Actions;

use App\Actions\v1\File\DownloadFile;
use App\Models\File;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Mockery;

beforeEach(function () {
    $this->action = new DownloadFile;
});

it('generates temporary url and returns redirect response', function () {
    $file = File::factory()->create([
        'name' => 'test-file.txt',
        'clean_name' => 'test.txt',
    ]);

    $expectedUrl = 'https://test-bucket.s3.amazonaws.com/test-file.txt';

    Storage::shouldReceive('disk')
        ->once()
        ->with('uploads')
        ->andReturn(Mockery::mock([
            'temporaryUrl' => $expectedUrl,
        ]));

    $response = $this->action->execute($file);

    expect($response)->toBeInstanceOf(RedirectResponse::class)
        ->and($response->getTargetUrl())->toBe($expectedUrl);
});
