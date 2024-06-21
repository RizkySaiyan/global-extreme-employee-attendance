<?php

namespace App\Services\Constant\Attendance;

use App\Services\Constant\BaseIDName;

class TimesheetConstant
{
    //default morning shift
    const DEFAULT_SHIFT_ID = 1;

    //Time limit for clock in and clock out in minutes
    const CLOCK_IN_START = -120;
    const CLOCK_IN_LIMIT = 240;

    const CLOCK_OUT_LIMIT = 300;
}
