<?php

namespace App\Exports\Excel;

use App\Models\Attendance\Timesheets;
use App\Models\Employee\Employee;
use App\Services\Constant\Attendance\TimesheetStatus;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TimesheetsExcel implements FromArray, WithMapping, WithHeadings
{
    public function __construct(public string $title, public array $data)
    {
    }

    public function array(): array
    {
        return $this->data;
    }

    public function map($row): array
    {
        return [
            $row['date'],
            $row['clockIn'],
            $row['clockOut'],
            $row['status'],
        ];;
    }

    public function headings(): array
    {
        return [
            'Employee Name',
            'Clock-In',
            'Clock-Out',
            'Status'
        ];
    }
}
