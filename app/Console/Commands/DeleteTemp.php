<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteTemp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:temp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the storage/app/tmp directory';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $files = Storage::disk('tmp')->allFiles();

        Storage::disk('tmp')->delete($files);

        $this->info(count($files).' temporary files deleted.');
    }
}
