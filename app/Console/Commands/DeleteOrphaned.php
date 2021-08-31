<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
    protected $description = 'Delete orphaned files from disk';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		$orphaned = \App\Models\File::doesntHave('lists')->get();


		// Delete from disk and database
		foreach($orphaned as $orphan) {
			\Storage::disk('uploads')->delete($orphan->name);
			$orphan->delete();
		}

		$this->info('Orphaned Files cleared successfully');
    }
}
