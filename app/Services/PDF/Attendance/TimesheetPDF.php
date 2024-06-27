<?php

namespace App\Services\PDF\Attendance;

use App\Models\Attendance\Timesheets;
use App\Parser\Attendance\TimesheetParser;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TimesheetPDF
{

    public function generate(Request $request)
    {
        $timesheets = Timesheets::ofDate('createdAt', $request->fromDate, $request->toDate)->get();

        $data = [
            'title' => 'Attendance Timesheets',
            'date' => $request->fromDate . "-" . $request->toDate,
            'timesheets' => TimesheetParser::get($timesheets)
        ];
        $pdf = Pdf::loadView('pdf.timesheet_pdf', $data)->setPaper('a4');
        return $pdf->download("Attendance_timesheets_" . $request->fromDate . "_" . $request->toDate . ".pdf");
    }
}
