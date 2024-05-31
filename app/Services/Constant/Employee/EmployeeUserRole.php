<?php

namespace App\Services\Constant\Employee;

use App\Services\Constant\BaseIDName;

class EmployeeUserRole extends BaseIDName
{
    const ADMIN_ID = 1;
    const ADMIN = 'admin';
    const USER_ID = 2;
    const USER = 'user';

    const OPTION = [
        self::ADMIN_ID => self::ADMIN,
        self::USER_ID => self::USER,
    ];

}
