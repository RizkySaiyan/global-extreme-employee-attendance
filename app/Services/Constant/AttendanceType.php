<?php

namespace App\Services\Constant;

use App\Services\Constant\BaseIDName;

class AttendanceType extends BaseIDName
{
    const DEFAULT_ID = 1;
    const DEFAULT = 'default';

    const OPTION = [
        self::DEFAULT_ID => self::DEFAULT,
    ];

}
