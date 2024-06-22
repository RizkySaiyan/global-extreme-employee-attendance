<?php

namespace App\Services\Constant\Attendance;

use App\Services\Constant\BaseIDName;

class TimesheetStatus extends BaseIDName
{
    const VALID_ID = 1;
    const VALID = 'valid';
    const LATE_CLOCK_IN_ID = 2;
    const LATE_CLOCK_IN = 'late clock in';
    const EARLY_CLOCK_OUT_ID = 3;
    const EARLY_CLOCK_OUT = 'early clock out';
    const NO_CLOCK_IN_ID = 4;
    const NO_CLOCK_IN = 'no clock in';
    const NO_CLOCK_OUT_ID = 5;
    const NO_CLOCK_OUT = 'no clock out';

    const OPTION = [
        self::VALID_ID => self::VALID,
        self::LATE_CLOCK_IN_ID => self::LATE_CLOCK_IN,
        self::EARLY_CLOCK_OUT_ID => self::EARLY_CLOCK_OUT,
        self::EARLY_CLOCK_OUT_ID => self::EARLY_CLOCK_OUT,
        self::NO_CLOCK_IN_ID => self::NO_CLOCK_IN,
        self::NO_CLOCK_OUT_ID => self::NO_CLOCK_OUT,
    ];
}
