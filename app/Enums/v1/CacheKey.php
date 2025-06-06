<?php

declare(strict_types=1);

namespace App\Enums\v1;

enum CacheKey: string
{
    case USER = 'user';
    case USERS = 'users';
    case GAME = 'game';
    case GAMES = 'games';
    case FILE = 'file';
    case FILES = 'files';
    case LOAD_ORDER = 'load-order';
    case LOAD_ORDERS = 'load-orders';

    public function with(string ...$keys): string
    {
        return implode(':', array_map(
            fn (string $key) => mb_strtolower($key),
            array_merge([$this->value], $keys)
        ));
    }
}
