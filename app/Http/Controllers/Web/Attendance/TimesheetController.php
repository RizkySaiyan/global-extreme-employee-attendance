<?php

namespace App\Http\Controllers\Web\Attendance;

use App\Algorithms\Attendance\TimesheetAlgo;
use App\Http\Controllers\Controller;
use App\Jobs\Attendance\GenerateAttendanceEmployeeJob;
use App\Models\Attendance\Timesheets;
use App\Services\PDF\Attendance\TimesheetPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function generateExcel(Request $request)
    {
        $user = Auth::user();
        GenerateAttendanceEmployeeJob::dispatch($request->fromDate, $request->toDate, $user->email);

        return success(internalMsg: "Excel will be sent to your email");
    }

    public function generatePdf(Request $request)
    {
        $service = new TimesheetPDF();
        return $service->generate($request);
    }
}
