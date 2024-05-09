<?php

namespace App\Console\Commands;

use App\Models\LoadOrder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteExpiredLists extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired lists';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $expired = LoadOrder::where('expires_at', '<', Carbon::now())->get();

        foreach ($expired as $list) {
            $list->delete();
        }

        $this->info(count($expired).' expired lists deleted.');
    }
}
