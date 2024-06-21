<?php

namespace App\Http\Controllers\Web\Attendance;

use App\Algorithms\Attendance\TimesheetAlgo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TimesheetController extends Controller
{
    public function attend()
    {
        $algo = new TimesheetAlgo();
        return $algo->attend();
    }
}
