<?php

namespace App\Console\Commands;

use App\Models\Backup;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Storage;

class DeleteBackups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:backups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired backups from the server.';

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
		$backups = Backup::where('expires_at', '<', Carbon::now())->get();

		foreach($backups as $backup) {
			Storage::disk('backup')->delete($backup->file);
			$backup->delete();
		}

		$this->info('Expired backups deleted successfully');
    }
}
