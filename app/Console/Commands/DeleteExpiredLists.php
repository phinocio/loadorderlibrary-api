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
	protected $description = 'Delete expired lists.';

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
		LoadOrder::where('expires_at', '<', Carbon::now())->delete();

		$this->info('Expired lists deleted successfully');
	}
}
