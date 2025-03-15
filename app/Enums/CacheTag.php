<?php

namespace App\Enums;

enum CacheTag: string
{
    case LOAD_ORDERS = 'load_orders';
    case LOAD_ORDER_ITEM = 'load_order_';
    case GAMES = 'games';
    case GAME_ITEM = 'game-';
    case STATS = 'stats';
    case FILES = 'files';
    case FILE_ITEM = 'file-';

    public function withSuffix(string|int $suffix): string
    {
        return $this->value . $suffix;
    }
}
