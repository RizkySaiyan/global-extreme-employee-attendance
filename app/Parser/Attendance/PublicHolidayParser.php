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
        return [
            'id' => $data->id,
            'name' => $data->name,
            'date' => $data->date,
            'assigned' => $data->isAssigned ? true : false,
        ];
    }
}
