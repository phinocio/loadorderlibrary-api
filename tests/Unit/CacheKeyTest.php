<?php

namespace Tests\Unit;

use App\Helpers\CacheKey;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class CacheKeyTest extends TestCase
{
    private string $path = "/v1/lists";
    private string $path2 = "/v1/lists/my-awesome-list";

    private array $query = [
        'page' => [
            'size' => 5,
        ],
        'filter' => [
            'game' => 'TESV Skyrim',
            'author' => 'Phinocio',
        ],
    ];

    private array $query2 = [
        'filter' => [
            'game' => 'TESV Skyrim',
            'author' => 'Phinocio',
        ],
        'page' => [
            'size' => 5,
        ],
    ];

    /**
    * This tests the following routes:
    * /v1/lists?page[size]=5&filter[game]=TESV Skyrim&filter[author]=Phinocio
    * /v1/lists?filter[game]=tesv skyrim&page[size]=5&filter[author]=phinocio
    * /v1/lists
    */
    public function test_it_generates_non_hashed_keys_correctly(): void
    {
        $this->assertEquals(CacheKey::create($this->path, $this->query, false), CacheKey::create($this->path, $this->query2, false));
        $this->assertEquals(CacheKey::create($this->path2, [], false), 'v1-lists-my-awesome-list');
        $this->assertNotEquals(CacheKey::create($this->path, $this->query, false), CacheKey::create($this->path2, $this->query, false));
        $this->assertFalse(Str::endsWith(CacheKey::create($this->path, [], false), '-'));
    }

    /**
    * This tests the following routes:
    * /v1/lists?page[size]=5&filter[game]=TESV Skyrim&filter[author]=Phinocio
    * /v1/lists?filter[game]=tesv skyrim&page[size]=5&filter[author]=phinocio
    */
    public function test_it_generates_hashed_keys_correctly(): void
    {
        $this->assertEquals(CacheKey::create($this->path, $this->query), CacheKey::create($this->path, $this->query2));
        $this->assertNotEquals(CacheKey::create($this->path, $this->query), CacheKey::create($this->path2, $this->query));
    }
}
