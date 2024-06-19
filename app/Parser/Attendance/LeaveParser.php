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

    public static function brief($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,
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

    public static function get($collections)
    {
        if (!$collections || count($collections) == 0) {
            return null;
        }

        $result = [];
        $employeeMap = [];

        foreach ($collections as $collection) {
            $employeeId = $collection->employeeId;

            if (!isset($employeeMap[$employeeId])) {
                $employeeMap[$employeeId] = count($result);

                $result[] = [
                    'employee' => [
                        'id' => $collection->employee->id,
                        'name' => $collection->employee->name,
                        'phone' => $collection->employee->phone
                    ],
                    'leaves' => []
                ];
            }

            $result[$employeeMap[$employeeId]]['leaves'][] = self::brief($collection);
        }
        return $result;
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
