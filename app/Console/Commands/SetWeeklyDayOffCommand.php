<?php

namespace App\Console\Commands;

use App\Jobs\Attendance\SetWeeklyDayOffJob;
use App\Jobs\SetWeeklyDayOffEmployeeJob;
use App\Models\Attendance\Schedule;
use App\Models\Employee\Employee;
use App\Services\Constant\Attendance\AttendanceType;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SetWeeklyDayOffCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:set-weekly-day-off-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $year = now()->addYear()->year;
        SetWeeklyDayOffJob::dispatch($year, null);
    }
}
