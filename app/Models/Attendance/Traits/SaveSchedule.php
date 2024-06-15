<?php

namespace App\Models\Attendance\Traits;

use App\Models\Attendance\Schedule;
use App\Services\Constant\Attendance\AttendanceType;

trait SaveSchedule
{
    public static function saveSchedule($scheduleable, $attributes)
    {
        $createdBy = [
            'createdBy' => $attributes['createdBy'],
            'createdByName' => $attributes['createdByName']
        ];
        if (isset($attributes['fromDate']) && isset($attributes['toDate'])) {
            $dates = generate_date_ranges($attributes['fromDate'], $attributes['toDate']);

            $attributes = [];
            foreach ($dates as $date) {
                Schedule::updateOrCreate([
                    'employeeId' => $scheduleable->employeeId,
                    'date' => $date
                ], [
                    'employeeId' => $scheduleable->employeeId,
                    'date' => $date,
                    'type' => AttendanceType::LEAVE_ID,
                    'referenceId' => $scheduleable->id,
                    'reference' => $scheduleable::class
                ] + $createdBy);
            }
        } else {
            Schedule::updateOrCreate([
                'employeeId' => $scheduleable->employeeId,
                'date' => $attributes['date']
            ], [
                'employeeId' => $scheduleable->employeeId,
                'date' => $attributes['date'],
                'type' => $attributes['type'],
                'referenceId' => $scheduleable->id,
                'reference' => $scheduleable::class
            ] + $createdBy);
        }
    }
}
