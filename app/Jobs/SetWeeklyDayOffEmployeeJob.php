<?php

namespace App\Jobs;

use App\Models\Attendance\Schedule;
use App\Models\Employee\Employee;
use App\Services\Constant\Attendance\AttendanceType;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SetWeeklyDayOffEmployeeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct($year)
    {
        $this->year = $year;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $sundays = [];
        $date = Carbon::create($this->year, 1, 1);

        if ($date->dayOfWeek != Carbon::SUNDAY) {
            $date->modify('next sunday');
        }

        while ($date->year == $this->year) {
            $sundays[] = $date->format('Y-m-d');
            $date->addWeek();
        }

        //TODO : add checks if employee already created schedule
        $employees = Employee::all();
        foreach ($employees as $employee) {
            foreach ($sundays as $sunday) {
                Schedule::create([
                    'employeeId' => $employee->id,
                    'date' => $sunday,
                    'type' => AttendanceType::WEEKLY_DAY_OFF_ID,
                    'reference' => null,
                    'createdBy' => 0,
                    'createdByName' => 'System'
                ]);
            }
        }
    }
}
