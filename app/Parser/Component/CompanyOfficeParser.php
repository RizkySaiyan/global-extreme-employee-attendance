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
            'departments' => self::briefs($data->departments)
        ];
    }

    public static function briefs($collections){

        if(!$collections || count($collections) == 0){
            return null;
        }

        $data = [];

        foreach($collections as $collection){
            $data[] = [
                'id' => $collection->id,
                'name' => $collection->name
            ];
        }

        return $data;
    }

}
