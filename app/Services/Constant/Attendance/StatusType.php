<?php

namespace App\Services\Constant\Attendance;

use App\Services\Constant\BaseIDName;

class StatusType extends BaseIDName
{
    const PENDING_ID = 1;
    const PENDING = 'pending';
    const APPROVED_ID = 2;
    const APPROVED = 'approved';

    const OPTION = [
        self::PENDING_ID => self::PENDING,
        self::APPROVED_ID => self::APPROVED,
    ];
}
