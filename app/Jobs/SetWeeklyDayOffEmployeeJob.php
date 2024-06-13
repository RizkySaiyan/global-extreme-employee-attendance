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
    public function __construct(public int $year, public ?Employee $employee)
    {
        if ($employee) {
            $this->employee = $employee;
        }
        $this->year = $year;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //TODO : Make this job run for setup one employee
        $sundays = [];
        $date = Carbon::create($this->year, 1, 1);
        if ($date->dayOfWeek != Carbon::SUNDAY) {
            $date->modify('next sunday');
        }

        while ($date->year == $this->year) {
            $sundays[] = $date->format('Y-m-d');
            $date->addWeek();
        }

        $query = Employee::query();
        if ($this->employee) {
            $query->where('id', $this->employee);
        }

        $employees = $query->whereDoesntHave('schedules')->get();
        foreach ($employees as $employee) {
            foreach ($sundays as $sunday) {
                Schedule::create([
                    'employeeId' => $employee->id,
                    'date' => $sunday,
                    'type' => AttendanceType::WEEKLY_DAY_OFF_ID,
                    'referenceId' => null,
                    'reference' => AttendanceType::WEEKLY_DAY_OFF,
                    'createdBy' => 'System',
                    'createdByName' => 'System',
                ]);
            }
        }
    }
}
