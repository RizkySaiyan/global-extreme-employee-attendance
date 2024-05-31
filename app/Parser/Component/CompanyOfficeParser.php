<?php

namespace App\Parser\Component;

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
            'departments' => self::departments($data->departments)
        ];
    }

    private static function departments($departments){
        
        if(!$departments || count($departments) == 0){
            return null;
        }

        $data = [];

        foreach($departments as $department){
            $data[] = [
                'id' => $department->id,
                'name' => $department->name
            ];
        }
        return $data;
    }
}
