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
            'id'=>$data->id,
            'code'=> $data->code,
            'name'=> $data->name,
        ];
    }

    public static function departmentMap($collections){

        if(!$collections || count($collections) == 0){
            return null;
        }

        $departments = Department::all();
        $existingIds = $collections->pluck('id')->toArray();
        $data = $departments->map(function ($department) use($existingIds){
            return [
                'assigned' => in_array($department->id, $existingIds),
                'id' => $department->id,
                'name' => $department->name
            ];
        })->toArray();
        return $data;
    }
}
