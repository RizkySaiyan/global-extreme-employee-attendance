<?php

namespace App\Parser\Attendance;

use App\Models\Attendance\Leave;
use App\Parser\Employee\EmployeeParser;
use App\Services\Constant\Attendance\LeaveBalance;
use App\Services\Constant\Attendance\LeaveConstant;
use App\Services\Constant\Attendance\StatusType;
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

        return [
            'id' => $data->id,
            'employee' => [
                'id' => $data->employee->id,
                'name' => $data->employee->name,
                'email' => $data->employee->user->email
            ],
            'fromDate' => $data->fromDate,
            'toDate' => $data->toDate,
            'notes' => $data->notes,
            'status' => StatusType::display($data->status),
            'approvedBy' => $data->approvedBy,
            'approvedByName' => $data->approvedByName,
            'createdBy' => $data->createdBy,
            'createdByName' => $data->createdByName,
            'createdAt' => $data->createdAt
        ];
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
            'balance' => LeaveConstant::LEAVE_BALANCE - $leaves,
            'used' => $leaves
        ];
    }
}
