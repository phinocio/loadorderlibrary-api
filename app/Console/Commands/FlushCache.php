<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

final class FlushCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:flush-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flush all application cache';

    /** Execute the console command. */
    public function handle(): void
    {
        Cache::flush();

        $this->info('All cache has been flushed successfully.');
    }
}
