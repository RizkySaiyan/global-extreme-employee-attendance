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
     * @param $collections
     *
     * @return array|null
     */
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
                    'schedules' => []
                ];
            }

            $result[$employeeMap[$employeeId]]['schedules'][] = self::brief($collection);
        }
        return $result;
    }

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
            'date' => $data->date,
            'type' => AttendanceType::display($data->type),
            'createdByName' => $data->createdByName,
            'reference' => self::referenceBrief($data->scheduleable)
        ];
    }

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
