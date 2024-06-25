<?php

namespace App\Jobs\Attendance;

use App\Models\Attendance\PublicHoliday;
use App\Models\Employee\EmployeeUser;
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
    public function __construct(public ?PublicHoliday $publicHoliday, public EmployeeUser $user)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->publicHoliday->assignPublicHoliday($this->user);
    }
}
