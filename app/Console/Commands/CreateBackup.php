<?php

namespace App\Console\Commands;

use App\Models\Backup;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Storage;
use ZipArchive;

class CreateBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a backup of the DB and existing uploaded files.';

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
		exec('php artisan down');
		$this->info('Site going into maintenance mode...');
		
		// Wait 3s to ensure anything in progress was compelted
		sleep(3);

		// Create the sql dump
		$user = config('database.connections.mysql.username');
		$pass = config('database.connections.mysql.password');
		$host = config('database.connections.mysql.host');
		$db = config('database.connections.mysql.database');
		$dumpName = "backup-" . Carbon::now()->format('Y-m-d') . ".sql";
		$command = "mysqldump --no-tablespaces --user=" . $user . " --password=" . $pass . " --host=" . $host . " " . $db . " > " . storage_path('app/tmp/') . $dumpName;
		$returnVar = NULL;
		$output  = NULL;
		exec($command, $output, $returnVar);

		// Get all existing file uploads
		$allUploads = Storage::disk('uploads')->files();

		// dd($allUploads);
		// Create the zip
		$zip = new ZipArchive;
		$zipFile ="backup-" . Carbon::now()->format('Y-m-d') . ".zip";
		if ($zip->open(storage_path('app/backup/' . $zipFile), ZipArchive::OVERWRITE | ZipArchive::CREATE)) {
			$zip->addFile(storage_path('app/tmp/' . $dumpName), $dumpName);
			foreach ($allUploads as $file) {
				$zip->addFile(storage_path('app/uploads/' . $file), 'uploads/' . $file);
			}

			$zip->close();
		}

		// Remove the temporary dump file
		Storage::disk('tmp')->delete($dumpName);

		// Create database entry
		$backup = new Backup();
		$backup->file = $zipFile;
		$backup->size = number_format(Storage::disk('backup')->size($zipFile) / 1000000, 2, '.', '');
		$backup->save();

		$this->info('Backup successfully created');

		exec('php artisan up');
    }
}
