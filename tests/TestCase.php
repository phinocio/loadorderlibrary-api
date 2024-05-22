<?php

namespace Tests;

use Database\Seeders\GameSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected string $seeder = GameSeeder::class;
}
