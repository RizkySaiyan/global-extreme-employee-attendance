<?php

namespace App\Services\Excel;

use App\Models\Attendance\Timesheets;
use App\Services\Constant\Attendance\TimesheetStatus;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TimesheetsExcel implements FromQuery, WithMapping, WithHeadings
{
    public function __construct(public $fromDate, public $toDate)
    {
    }

    public function query()
    {
        return Timesheets::ofDate('createdAt', $this->fromDate, $this->toDate);
    }

    public function map($timesheet): array
    {
        return [
            $timesheet->employee->name,
            $timesheet->shift->name,
            $timesheet->clockIn,
            $timesheet->clockOut,
            TimesheetStatus::display($timesheet->status)
        ];
    }

    public function headings(): array
    {
        return [
            'Employee Name',
            'Shift',
            'Clock-In',
            'Clock-Out',
            'Status'
        ];
    }
}
