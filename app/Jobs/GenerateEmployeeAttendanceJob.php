<?php

namespace App\Jobs;

use App\Exports\Excel\TimesheetsExcel;
use App\Services\Constant\Path;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateEmployeeAttendanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(string $fromDate, string $toDate, $employee)
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->employee = $employee;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $filePath = Path::EMPLOYEE_TIMESHEET . "/{$this->employee->name}_{$this->fromDate}_{$this->toDate}.xlsx";
        Excel::store(new TimesheetsExcel($this->fromDate, $this->toDate, $this->employee->name), $filePath, 'local');

        $fileUrl = Storage::url($filePath);
        $fullFilePath = storage_path('app/' . $filePath);

        // Send email with the file attached
        Mail::send([], [], function ($message) use ($fileUrl, $fullFilePath) {
            $message->to($this->employee->email)
                ->subject("Your timesheet")
                ->attach($fullFilePath, [
                    'as' => "{$this->employee->name}_{$this->fromDate}_{$this->toDate}.xlsx",
                    'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]);
        });

        // Delete the file after sending email
        Storage::delete($filePath);
    }
}
