<?php

declare(strict_types=1);

use App\Models\Game;
use App\Models\LoadOrder;
use App\Models\User;

beforeEach(function () {
    $this->game = Game::factory()->create(['name' => 'Test Game']);
    $this->author = User::factory()->create(['name' => 'Test Author']);
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->loadOrder = LoadOrder::factory()->create([
        'name' => 'Test Load Order',
        'slug' => 'test-load-order',
        'game_id' => $this->game->id,
        'user_id' => $this->author->id,
        'is_private' => false,
    ])->fresh();
});

describe('destroy', function () {
    it('allows admin to delete any load order', function () {
        login($this->admin)
            ->deleteJson("/v1/admin/lists/{$this->loadOrder->slug}")
            ->assertNoContent();

        $this->assertDatabaseMissing('load_orders', [
            'slug' => $this->loadOrder->slug,
        ]);
    });

    it('prevents non-admin from deleting load order', function () {
        login($this->author)
            ->deleteJson("/v1/admin/lists/{$this->loadOrder->slug}")
            ->assertForbidden();
    });

    it('prevents guest from deleting load order', function () {
        guest()
            ->deleteJson("/v1/admin/lists/{$this->loadOrder->slug}")
            ->assertUnauthorized();
    });

    it('returns 404 when deleting non-existent load order', function () {
        login($this->admin)
            ->deleteJson('/v1/admin/lists/non-existent-load-order')
            ->assertNotFound();
    });
});
