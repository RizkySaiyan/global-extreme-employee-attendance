<?php

namespace App\Jobs;

use App\Models\Attendance\PublicHoliday;
use App\Models\Attendance\Schedule;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeUser;
use App\Services\Constant\Attendance\AttendanceType;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AssignPublicHolidayJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public ?PublicHoliday $publicHoliday = null, public ?EmployeeUser $user = null)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $employees = Employee::whereDoesntHave('schedules', function ($query) {
            $query->whereDate('date', $this->publicHoliday->date);
        })->where('isResign', false)->get();

        foreach ($employees as $employee) {
            Schedule::create([
                'employeeId' => $employee->id,
                'date' => $this->publicHoliday->date,
                'type' => AttendanceType::PUBLIC_HOLIDAY_ID,
                'referenceId' => $this->publicHoliday->id,
                'reference' => PublicHoliday::class,
                'createdBy' => $this->user->employeeId,
                'createdByName' => $this->user->employee->name,
            ]);
        }
        $this->publicHoliday->update([
            'isAssigned' => true,
        ]);
    }
}
