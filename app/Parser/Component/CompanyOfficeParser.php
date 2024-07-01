<?php

namespace App\Parser\Component;

use App\Models\Component\Department;
use GlobalXtreme\Parser\BaseParser;

class CompanyOfficeParser extends BaseParser
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
            'code' => $data->code,
            'name' => $data->name,
        ];
    }
}
