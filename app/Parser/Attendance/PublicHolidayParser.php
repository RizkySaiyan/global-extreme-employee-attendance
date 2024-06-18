<?php

namespace App\Parser\Attendance;

use App\Models\Attendance\Schedule;
use GlobalXtreme\Parser\BaseParser;

class PublicHolidayParser extends BaseParser
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
        $schedule = $data->schedule()->where('date', $data->date);
        return [
            'assigned' => $schedule->exists() ? true : false,
            'name' => $data->name,
            'date' => $data->date
        ];
    }
}
