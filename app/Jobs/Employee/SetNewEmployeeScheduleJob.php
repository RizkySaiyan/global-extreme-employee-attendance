<?php

namespace App\Jobs\Employee;

use App\Models\Attendance\PublicHoliday;
use App\Models\Attendance\Schedule;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SetNewEmployeeScheduleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $year, public Employee $employee, public ?EmployeeUser $user)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Schedule::setWeeklyDayOff($this->year, $this->employee, $this->user);
        PublicHoliday::setPublicHolidayNewEmployee($this->year, $this->employee, $this->user);
    }
}
