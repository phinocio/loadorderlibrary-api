<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
		$files = \Storage::disk('tmp')->allFiles();

		\Storage::disk('tmp')->delete($files);
		
		$this->info('Files cleared successfully');
    }
}
