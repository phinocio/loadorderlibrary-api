<?php

namespace App\Console\Commands;

use App\Models\File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteOrphaned extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:orphaned';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete orphaned files';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $orphaned = File::doesntHave('lists')->get();

        foreach ($orphaned as $file) {
            Storage::disk('uploads')->delete($file->name);
            $file->delete();
        }

        $this->info(count($orphaned).' orphaned files deleted.');
    }
}
