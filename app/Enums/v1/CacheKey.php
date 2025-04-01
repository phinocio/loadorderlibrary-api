<?php

declare(strict_types=1);

namespace App\Enums\v1;

enum CacheKey: string
{
    case USER = 'user';
    case USERS = 'users';

    public function with(string ...$keys): string
    {
        return implode(':', array_merge([$this->value], $keys));
    }
}
