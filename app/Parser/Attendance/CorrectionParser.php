<?php

namespace App\Parser\Attendance;

use App\Services\Constant\Attendance\TimesheetStatus;
use GlobalXtreme\Parser\BaseParser;

class CorrectionParser extends BaseParser
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
            'employeeId' => $data->employeeId,
            'date' => $data->date,
            'clockIn' => $data->clockIn,
            'clockOut' => $data->clockOut,
            'createdAt' => $data->createdAt,
            'timesheet' => [
                'id' => $data->timesheet->id,
                'clockIn' => $data->timesheet->clockIn,
                'clockOut' => $data->timesheet->clockOut,
                'shift' => $data->timesheet->shift,
                'status' => TimesheetStatus::display($data->timesheet->status),
            ]
        ];
    }
}
