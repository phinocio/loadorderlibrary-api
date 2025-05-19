<?php

declare(strict_types=1);

namespace Tests\Unit\v1\Actions\LoadOrder;

use App\Actions\v1\File\UploadFile;
use App\Actions\v1\LoadOrder\CreateLoadOrder;
use App\Models\Game;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('uploads');
    $this->action = new CreateLoadOrder(new UploadFile);

    $this->game = Game::factory()->create(['name' => 'Test Game']);
    $this->author = User::factory()->create(['name' => 'Test Author']);
});

test('it creates load order with basic information', function () {
    Auth::partialMock();
    Auth::shouldReceive('id')->andReturn($this->author->id);
    Auth::shouldReceive('check')->andReturn(true);

    $result = $this->action->execute([
        'name' => 'New Load Order',
        'description' => 'Test Description',
        'version' => '1.0.0',
        'website' => 'https://example.com',
        'discord' => 'https://discord.example.com',
        'readme' => 'http://readme.example.com',
        'private' => true,
        'game' => $this->game->id,
        'files' => [
            UploadedFile::fake()->createWithContent('modlist.txt', "Skyrim.esm\nUpdate.esm"),
        ],
    ]);

    expect($result)
        ->name->toBe('New Load Order')
        ->description->toBe('Test Description')
        ->version->toBe('1.0.0')
        ->website->toBe('example.com')
        ->discord->toBe('discord.example.com')
        ->readme->toBe('readme.example.com')
        ->is_private->toBeTrue()
        ->game_id->toBe($this->game->id)
        ->user_id->toBe($this->author->id);

    expect($result->files)->toHaveCount(1)
        ->and($result->files->first()->clean_name)->toBe('modlist.txt');
});

test('it sets optional fields to null when not provided', function () {
    Auth::partialMock();
    Auth::shouldReceive('id')->andReturn($this->author->id);
    Auth::shouldReceive('check')->andReturn(true);

    $result = $this->action->execute([
        'name' => 'New Load Order',
        'game' => $this->game->id,
        'files' => [
            UploadedFile::fake()->createWithContent('modlist.txt', "Skyrim.esm\nUpdate.esm"),
        ],
    ]);

    expect($result)
        ->name->toBe('New Load Order')
        ->description->toBeNull()
        ->version->toBeNull()
        ->website->toBeNull()
        ->discord->toBeNull()
        ->readme->toBeNull()
        ->is_private->toBeFalse()
        ->game_id->toBe($this->game->id)
        ->user_id->toBe($this->author->id);
});

test('it handles multiple files', function () {
    Auth::partialMock();
    Auth::shouldReceive('id')->andReturn($this->author->id);
    Auth::shouldReceive('check')->andReturn(true);

    $result = $this->action->execute([
        'name' => 'New Load Order',
        'game' => $this->game->id,
        'files' => [
            UploadedFile::fake()->createWithContent('modlist.txt', "Skyrim.esm\nUpdate.esm"),
            UploadedFile::fake()->createWithContent('plugins.txt', "plugin1.esp\nplugin2.esp"),
        ],
    ]);

    expect($result->files)->toHaveCount(2)
        ->and($result->files->pluck('clean_name')->all())->toContain('modlist.txt', 'plugins.txt');
});

test('it handles all expiration options for authenticated users', function () {
    Auth::partialMock();
    Auth::shouldReceive('id')->andReturn($this->author->id);
    Auth::shouldReceive('check')->andReturn(true);

    $testCases = [
        '3h' => now()->addHours(3),
        '3d' => now()->addDays(3),
        '1w' => now()->addWeek(),
        '1m' => now()->addMonth(),
        'never' => null,
        null => null,
        'invalid' => null,
    ];

    foreach ($testCases as $expires => $expected) {
        $result = $this->action->execute([
            'name' => 'New Load Order',
            'game' => $this->game->id,
            'expires' => $expires,
            'files' => [
                UploadedFile::fake()->createWithContent('modlist.txt', "Skyrim.esm\nUpdate.esm"),
            ],
        ]);

        if ($expected === null) {
            expect($result->expires_at)->toBeNull();
        } else {
            // Allow 1 second difference to handle slight timing variations
            expect($result->expires_at->timestamp)
                ->toBeGreaterThanOrEqual($expected->timestamp)
                ->toBeLessThanOrEqual($expected->timestamp + 1);
        }
    }
});

test('it handles all expiration options for guests', function () {
    Auth::partialMock();
    Auth::shouldReceive('id')->andReturn(null);
    Auth::shouldReceive('check')->andReturn(false);

    $testCases = [
        '3h' => now()->addHours(3),
        '3d' => now()->addDays(3),
        '1w' => now()->addWeek(),
        '1m' => now()->addMonth(),
        'never' => null,
        null => now()->addHours(24),
        'invalid' => now()->addHours(24),
    ];

    foreach ($testCases as $expires => $expected) {
        $result = $this->action->execute([
            'name' => 'New Load Order',
            'game' => $this->game->id,
            'expires' => $expires,
            'files' => [
                UploadedFile::fake()->createWithContent('modlist.txt', "Skyrim.esm\nUpdate.esm"),
            ],
        ]);

        if ($expected === null) {
            expect($result->expires_at)->toBeNull();
        } else {
            // Allow 1 second difference to handle slight timing variations
            expect($result->expires_at->timestamp)
                ->toBeGreaterThanOrEqual($expected->timestamp)
                ->toBeLessThanOrEqual($expected->timestamp + 1);
        }
    }
});
