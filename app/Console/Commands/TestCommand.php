<?php

namespace App\Console\Commands;

use App\Models\Attendance\Leave;
use App\Models\Attendance\Schedule;
use App\Models\Employee\Employee;
use App\Services\Constant\Attendance\AttendanceType;
use App\Services\Number\Generator\EmployeeNumber;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'dev-test';
    protected $description = '';

    public function handle()
    {
        $sundays = [];
        $date = Carbon::create(now()->year, 1, 1);

        if ($date->dayOfWeek != Carbon::SUNDAY) {
            $date->modify('next sunday');
        }

        while ($date->year == now()->year) {
            $sundays[] = $date->format('Y-m-d');
            $date->addWeek();
        }

        $employees = Employee::whereDoesntHave('schedules', function ($query) {
            $query->where('type', AttendanceType::WEEKLY_DAY_OFF_ID);
        })->get();
        foreach ($employees as $employee) {
            foreach ($sundays as $sunday) {
                Schedule::create([
                    'employeeId' => $employee->id,
                    'date' => $sunday,
                    'type' => AttendanceType::WEEKLY_DAY_OFF_ID,
                    'referenceId' => null,
                    'reference' => AttendanceType::WEEKLY_DAY_OFF,
                    'createdBy' => 0,
                    'createdBy' => 'System'
                ]);
            }
        }

        // $year = now()->year;

        // $leave = Leave::where('employeeId', 1)
        //     ->whereNotNull('approvedBy')
        //     ->whereYear('fromDate', $year)
        //     ->whereYear('toDate', $year)
        //     ->pluck('toDate', 'fromDate');


        // $totalLeaves = 0;
        // $leave->each(function ($endDate, $startDate) use (&$totalLeaves) {
        //     $start = Carbon::parse($startDate);
        //     $end = Carbon::parse($endDate);

        //     $days = $start->diffInDays($end);
        //     $totalLeaves += $days;
        // });
    }
}
