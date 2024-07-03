<?php

namespace App\Parser\Attendance;

use App\Models\Attendance\Timesheets;
use App\Services\Constant\Attendance\StatusType;
use App\Services\Constant\Attendance\TimesheetStatus;
use GlobalXtreme\Parser\BaseParser;

class TimesheetParser extends BaseParser
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
            'shift' => [
                'id' => $data->shift->id,
                'name' => $data->shift->name,
                'startTime' => $data->shift->startTime,
                'endTime' => $data->shift->endTime
            ],
            'clockIn' => [
                'date' => $data->clockIn ?  $data->clockIn->format('d/m/y') : null,
                'time' => $data->clockIn ?  $data->clockIn->format('H:i:s') : null,
                'minuteLate' => $data->minuteLate
            ],
            'clockOut' => [
                'date' => $data->clockOut ? $data->clockOut->format('d/m/y') : null,
                'time' => $data->clockOut ? $data->clockOut->format('H:i:s') : null,
                'minuteEarly' => $data->minuteEarly
            ],
            'status' => [
                'id' => $data->status,
                'status' => TimesheetStatus::display($data->status)
            ],
            'correction' => $data->correction ? [
                'id' => $data->correction->id,
                'notes' => $data->correction->notes,
                'clockIn' => $data->correction->clockIn,
                'clockOut' => $data->correction->clockOut,

                'status' => StatusType::display($data->correction->status),
            ]
                : null,
            'createdBy' => $data->createdByName
        ];
    }
}
