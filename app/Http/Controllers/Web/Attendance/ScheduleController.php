<?php

namespace App\Http\Controllers\Web\Attendance;

use App\Algorithms\Attendance\ScheduleAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\ScheduleRequest;
use App\Models\Attendance\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function get(Request $request)
    {
        $schedule = Schedule::getOrPaginate($request);
        return success($schedule);
    }

    public function create(ScheduleRequest $request)
    {
        $algo = new ScheduleAlgo();
        return $algo->create($request);
    }
}
