<?php

namespace App\Enums;

enum CacheTag: string
{
    case LOAD_ORDERS = 'load_orders';
    case LOAD_ORDER_ITEM = 'load_order_';
    case GAMES = 'games';
    case GAME_ITEM = 'game-';
    case STATS = 'stats';

    /**
     * Get the tag with an optional suffix
     */
    public function withSuffix(string|int $suffix = null): string
    {
        return $suffix !== null ? $this->value . $suffix : $this->value;
    }
}
