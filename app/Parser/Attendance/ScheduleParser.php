<?php

namespace App\Parser\Attendance;

use App\Models\Attendance\Leave;
use App\Models\Attendance\PublicHoliday;
use App\Models\Attendance\Shift;
use App\Services\Constant\Attendance\AttendanceType;
use App\Services\Constant\Attendance\StatusType;
use GlobalXtreme\Parser\BaseParser;

class ScheduleParser extends BaseParser
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

        $result = [
            'employee' => [
                'id' => $data->employee->id,
                'name' => $data->employee->name,
                'phone' => $data->employee->phone
            ],
            'schedules' => [
                'id' => $data->id,
                'date' => $data->date,
                'type' => AttendanceType::display($data->type),
                'createdByName' => $data->createdByName,
                'reference' => self::referenceBrief($data->scheduleable)

            ]
        ];
        return $result;
    }

    /** FUNCTION */
    public static function referenceBrief($reference)
    {
        if (!$reference) {
            return null;
        }

        $result = [];
        switch (get_class($reference)) {
            case PublicHoliday::class:
                $result = [
                    'id' => $reference->id,
                    'name' => $reference->name,
                    'date' => $reference->date,
                    'createdByName' => $reference->createdByName
                ];
            case Shift::class:
                $result = [
                    'id' => $reference->id,
                    'name' => $reference->name,
                    'startTime' => $reference->startTime,
                    'endTime' => $reference->endTime,
                    'createdByName' => $reference->createdByName
                ];
                break;
            case Leave::class:
                $result = [
                    'id' => $reference->id,
                    'notes' => $reference->notes,
                    'fromDate' => $reference->fromDate,
                    'toDate' => $reference->toDate,
                    'status' => StatusType::display($reference->status),
                    'approvedByName' => $reference->approvedByName,
                    'createdByName' => $reference->createdByName
                ];
                break;
            default:
                $result = null;
                break;
        }
        return $result;
    }
}
