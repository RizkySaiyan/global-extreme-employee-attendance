<?php

namespace App\Http\Controllers\Web\Attendance;

use App\Algorithms\Attendance\TimesheetAlgo;
use App\Http\Controllers\Controller;
use App\Jobs\GenerateEmployeeAttendanceJob;
use App\Models\Attendance\Timesheets;
use App\Models\Employee\Employee;
use Illuminate\Http\Request;

class TimesheetController extends Controller
{
    public function get(Request $request)
    {
        $timesheets = Timesheets::filter($request)->getOrPaginate($request);
        return success($timesheets);
    }

    public function attend()
    {
        $algo = new TimesheetAlgo();
        return $algo->attend();
    }

    public function generateTimesheetExcel(Request $request)
    {
        $employees = Employee::all();

        foreach ($employees as $employee) {
            GenerateEmployeeAttendanceJob::dispatch($request->fromDate, $request->toDate, $employee);
        }

        return success();
    }
}
