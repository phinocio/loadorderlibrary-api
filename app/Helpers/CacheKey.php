<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class CacheKey
{
    public static function create(string $path, array $query, bool $hash = true): string
    {
        $normalizedPath = trim(str_replace('/', '-', $path), '-');

        ksort($query);

        $queryString = collect($query)
            ->map(function ($values, $key) {
                if (is_array($values)) {
                    ksort($values);
                    return collect($values)
                        ->map(fn ($value, $subKey) => "{$key}-{$subKey}-{$value}")
                        ->implode('-');
                }
                return "{$key}-" . trim($values, '/');
            })
            ->implode('-');

        $key = Str::lower($queryString ? "{$normalizedPath}-{$queryString}" : $normalizedPath);

        return $hash ? md5($key) : $key;
    }
}
