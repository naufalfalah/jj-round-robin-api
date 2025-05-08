<?php

namespace App\Console\Commands;

use App\Models\ApiLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteOldApiLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apilog:delete-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete API logs older than 2 months';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $twoMonthsAgo = Carbon::now()->subMonths(2);

        $deletedLogs = ApiLog::where('created_at', '<', $twoMonthsAgo)->delete();

        $this->info("Deleted $deletedLogs API log(s) older than 2 months.");

        return 0;
    }
}
