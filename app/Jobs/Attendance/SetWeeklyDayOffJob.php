<?php

namespace App\Jobs\Attendance;

use App\Models\Attendance\Schedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SetWeeklyDayOffJob implements ShouldQueue
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
        Schedule::setWeeklyDayOff($this->year, $this->employee);
    }
}
