<?php

namespace App\Jobs\Employee;

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
    public function __construct(public Employee $employee)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->employee->attendances()->delete();
    }
}
