<?php

declare(strict_types=1);

use App\Models\Game;

beforeEach(function () {
    $this->game = Game::factory()->create(['name' => 'Test Game', 'slug' => 'test-game'])->fresh();
    $this->otherGame = Game::factory()->create(['name' => 'Other Game', 'slug' => 'other-game'])->fresh();
});

describe('index', function () {
    it('allows anyone to view games list', function () {
        $response = $this->getJson('/v1/games');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'lists_count',
                    ],
                ],
            ])
            ->assertJsonCount(2, 'data');
    });
});

describe('show', function () {
    it('allows viewing a game by slug', function () {
        $response = $this->getJson("/v1/games/{$this->game->slug}");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'lists_count',
                ],
            ])
            ->assertJsonPath('data.name', $this->game->name)
            ->assertJsonPath('data.slug', $this->game->slug);
    });

    it('allows viewing a game by name', function () {
        $response = $this->getJson("/v1/games/{$this->game->name}");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'lists_count',
                ],
            ])
            ->assertJsonPath('data.name', $this->game->name)
            ->assertJsonPath('data.slug', $this->game->slug);
    });

    it('returns 404 when game does not exist', function () {
        $this->getJson('/v1/games/nonexistent')
            ->assertNotFound();
    });
});
