<?php

namespace App\Parser\Attendance;

use App\Models\Attendance\Leave;
use App\Services\Constant\Attendance\LeaveBalance;
use GlobalXtreme\Parser\BaseParser;
use Illuminate\Support\Facades\Auth;

class LeaveParser extends BaseParser
{
    /**
     * @param $data
     *
     * @return array|null
     */
    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        return parent::first($data);
    }

    public static function balance($data)
    {
        if (!$data) {
            return null;
        }

        $leaves = Leave::getEmployeeLeaves($data->employeeId);

        return [
            'employeeId' => $data->employeeId,
            'name' => $data->employee->name,
            'balance' => LeaveBalance::TOTAL_LEAVES - $leaves,
            'used' => $leaves
        ];
    }
}
