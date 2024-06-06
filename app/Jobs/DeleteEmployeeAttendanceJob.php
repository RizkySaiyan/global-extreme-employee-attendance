<?php

namespace App\Jobs;

use App\Models\Employee\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteEmployeeAttendanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $employeeId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $employee = Employee::find($this->employeeId);
        if ($employee) {
            $employee->deleteAttendance();
        }
    }
}
