<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\LoadOrder;
use Illuminate\Console\Command;

final class DeleteExpiredLists extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lists:delete-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired lists';

    /** Execute the console command. */
    public function handle(): void
    {
        $lists = LoadOrder::query()->expired()->get();

        foreach ($lists as $list) {
            $list->delete();
        }

        $this->info('Expired lists deleted successfully.');
    }
}
