<?php

namespace App\Console\Commands;

use App\Models\TimeSheet;
use Illuminate\Console\Command;

class ProcessCheckoutsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkout:pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending checkouts';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // $check_in = TimeSheet::whereNull('end_time')->get();
        //     foreach($check_in as $items){

        //         $data = TimeTracking::join('time_sheets', 'time_trackings.id', '=', 'time_sheets.time_tracking_id')
        //         ->where('time_sheets.id', $items->id)
        //         ->select('time_trackings.user_id', 'time_trackings.date')
        //         ->get();
        // }
        $check_ins = TimeSheet::with('time_track.user.employee.branch')
            ->whereNull('end_time')
            ->get();
        if ($check_ins->count() > 0) {
            foreach ($check_ins as $check_in) {
                $check_out_time = $check_in->time_track->user->employee->branch->check_out;
                $check_out = now()->subDays(1)->format('Y-m-d ').$check_out_time;
                TimeSheet::where('id', $check_in->id)->update([
                    'end_time' => $check_out,
                ]);
            }
        }
        $this->info($check_ins);
    }
}
