<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\v1\LoadOrder;

use App\Actions\v1\File\UploadFile;
use App\Actions\v1\LoadOrder\UpdateLoadOrder;
use App\Models\Game;
use App\Models\LoadOrder;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('uploads');
    $this->action = new UpdateLoadOrder(new UploadFile);

    $this->game = Game::factory()->create(['name' => 'Test Game']);
    $this->author = User::factory()->create(['name' => 'Test Author']);
    $this->loadOrder = LoadOrder::factory()->create([
        'name' => 'Original Name',
        'description' => 'Original Description',
        'version' => '1.0.0',
        'website' => 'https://example.com',
        'discord' => 'https://discord.example.com',
        'readme' => 'https://readme.example.com',
        'is_private' => false,
        'game_id' => $this->game->id,
        'user_id' => $this->author->id,
    ]);
});

test('it updates basic load order information', function () {
    $result = $this->action->execute($this->loadOrder, [
        'name' => 'Updated Name',
        'description' => 'Updated Description',
        'version' => '2.0.0',
        'website' => 'https://new.example.com',
        'discord' => 'https://discord.new.example.com',
        'readme' => 'https://readme.new.example.com',
        'private' => true,
    ]);

    expect($result)
        ->name->toBe('Updated Name')
        ->description->toBe('Updated Description')
        ->version->toBe('2.0.0')
        ->website->toBe('https://new.example.com')
        ->discord->toBe('https://discord.new.example.com')
        ->readme->toBe('https://readme.new.example.com')
        ->is_private->toBeTrue();
});

test('it updates files when provided', function () {
    $uploadedFile = UploadedFile::fake()->createWithContent('modlist.txt', "Skyrim.esm\nUpdate.esm");

    $result = $this->action->execute($this->loadOrder, [
        'files' => [$uploadedFile],
    ]);

    expect($result->files)->toHaveCount(1)
        ->and($result->files->first()->clean_name)->toBe('modlist.txt');
});

test('it allows partial updates', function () {
    $result = $this->action->execute($this->loadOrder, [
        'name' => 'Updated Name',
    ]);

    expect($result)
        ->name->toBe('Updated Name')
        ->description->toBe('Original Description')
        ->version->toBe('1.0.0')
        ->website->toBe('https://example.com')
        ->discord->toBe('https://discord.example.com')
        ->readme->toBe('https://readme.example.com')
        ->is_private->toBeFalse();
});

test('it updates game when provided', function () {
    $newGame = Game::factory()->create(['name' => 'New Game']);

    $result = $this->action->execute($this->loadOrder, [
        'game' => $newGame->id,
    ]);

    expect($result->game_id)->toBe($newGame->id);
});

test('it sets expiration based on expires parameter', function () {
    Auth::shouldReceive('check')->andReturn(true);

    $result = $this->action->execute($this->loadOrder, [
        'expires' => '3h',
    ]);

    expect($result->expires_at->toDateTimeString())
        ->toBe(now()->addHours(3)->toDateTimeString());
});

test('it preserves expiration when not provided', function () {
    $originalExpiration = now()->addDay();
    $this->loadOrder->update(['expires_at' => $originalExpiration]);

    $result = $this->action->execute($this->loadOrder, [
        'name' => 'Updated Name',
    ]);

    expect($result->expires_at->toDateTimeString())
        ->toBe($originalExpiration->toDateTimeString());
});

test('it handles all expiration options for authenticated users', function () {
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
        $result = $this->action->execute($this->loadOrder, [
            'expires' => $expires,
        ]);

        if ($expected === null) {
            expect($result->expires_at)->toBeNull();
        } else {
            expect($result->expires_at->toDateTimeString())
                ->toBe($expected->toDateTimeString());
        }
    }
});

test('it handles all expiration options for guests', function () {
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
        $result = $this->action->execute($this->loadOrder, [
            'expires' => $expires,
        ]);

        if ($expected === null) {
            expect($result->expires_at)->toBeNull();
        } else {
            expect($result->expires_at->toDateTimeString())
                ->toBe($expected->toDateTimeString());
        }
    }
});
