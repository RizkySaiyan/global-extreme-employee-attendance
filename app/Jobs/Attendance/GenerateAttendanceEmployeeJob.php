<?php

namespace App\Jobs\Attendance;

use App\Services\Constant\Path\Path;
use App\Services\Excel\TimesheetsExcel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class GenerateAttendanceEmployeeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public $fromDate, public $toDate, public $email)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $filePath = Path::EMPLOYEE_TIMESHEET . "/Timesheet_excel.xlsx";
        Excel::store(new TimesheetsExcel($this->fromDate, $this->toDate), $filePath, 'local');

        $fullFilePath = storage_path('app/' . $filePath);

        // Send email with the file attached
        Mail::send('emails.default', [], function ($message) use ($fullFilePath) {
            $message->to($this->email)
                ->subject("Your timesheet")
                ->attach($fullFilePath, [
                    'as' => "{{$this->fromDate}_{$this->toDate}.xlsx",
                    'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]);
        });

        // Delete the file after sending email
        Storage::delete($filePath);
    }
}
