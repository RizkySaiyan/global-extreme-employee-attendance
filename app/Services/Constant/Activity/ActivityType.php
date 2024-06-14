<?php

namespace App\Services\Constant\Activity;

use App\Services\Constant\BaseCodeName;

class ActivityType extends BaseCodeName
{
    const GENERAL = 'general';
    const COMPONENT = 'component';
    const EMPLOYEE = 'employee';
    const SHIFT = 'shift';
    const PUBLIC_HOLIDAY = 'public_holiday';
    const LEAVE = 'Leave';

    const OPTION = [
        self::GENERAL,
        self::COMPONENT,
        self::EMPLOYEE,
        self::SHIFT,
        self::PUBLIC_HOLIDAY,
        self::LEAVE
    ];
}
