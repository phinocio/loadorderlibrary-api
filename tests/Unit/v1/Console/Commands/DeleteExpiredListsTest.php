<?php

declare(strict_types=1);

use App\Models\LoadOrder;

it('deletes expired lists', function () {
    // Create expired load orders (dates in the past)
    $expiredLoadOrders = LoadOrder::factory()->count(3)->create([
        'expires_at' => now()->subDays(1),
    ]);

    // Create a non-expired load order (date in the future)
    $activeLoadOrder = LoadOrder::factory()->create([
        'expires_at' => now()->addDays(1),
    ]);

    // Create a load order without expiration
    $noExpirationLoadOrder = LoadOrder::factory()->create([
        'expires_at' => null,
    ]);

    // Execute the command
    $this->artisan('lists:delete-expired')
        ->expectsOutput('Expired lists deleted successfully.')
        ->assertSuccessful();

    // Assert expired lists are deleted
    foreach ($expiredLoadOrders as $loadOrder) {
        $this->assertDatabaseMissing('load_orders', [
            'id' => $loadOrder->id,
        ]);
    }

    // Assert non-expired lists remain
    $this->assertDatabaseHas('load_orders', [
        'id' => $activeLoadOrder->id,
    ]);

    // Assert lists without expiration remain
    $this->assertDatabaseHas('load_orders', [
        'id' => $noExpirationLoadOrder->id,
    ]);
});

it('handles no expired lists gracefully', function () {
    // Create only non-expired load orders
    LoadOrder::factory()->create([
        'expires_at' => now()->addDays(1),
    ]);

    LoadOrder::factory()->create([
        'expires_at' => null,
    ]);

    // Execute the command
    $this->artisan('lists:delete-expired')
        ->expectsOutput('Expired lists deleted successfully.')
        ->assertSuccessful();

    // Verify the database still contains the correct number of records
    $this->assertDatabaseCount('load_orders', 2);
});
