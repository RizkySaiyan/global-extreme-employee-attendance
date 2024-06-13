<?php

namespace App\Services\Constant\Attendance;

use App\Services\Constant\BaseIDName;

class AttendanceType extends BaseIDName
{
    const WEEKLY_DAY_OFF_ID = 1;
    const WEEKLY_DAY_OFF = 'Weekly day off';
    const PUBLIC_HOLIDAY_ID = 2;
    const PUBLIC_HOLIDAY = 'Public Holiday';
    const LEAVE_ID = 3;
    const LEAVE = 'Leave';
    const SHIFT_ID = 4;
    const SHIFT = 'Shift';

    const OPTION = [
        self::WEEKLY_DAY_OFF_ID => self::WEEKLY_DAY_OFF,
        self::PUBLIC_HOLIDAY_ID => self::PUBLIC_HOLIDAY,
        self::LEAVE_ID => self::LEAVE,
        self::SHIFT_ID => self::SHIFT,
    ];
}
