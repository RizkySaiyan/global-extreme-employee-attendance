<?php

namespace App\Parser\Employee;

use GlobalXtreme\Parser\BaseParser;

class EmployeeParser extends BaseParser
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
        $siblings['siblings'] = static::siblings($data['siblings']);
    
        return array_merge(parent::first($data), $siblings);
    }
    

    private static function siblings($collections){

        if (!$collections || count($collections) == 0) {
            return null;
        }

        $sibling = [];
        foreach ($collections as $collection) {
            $sibling[] = [
                'id' => $collection->id,
                'name' => $collection->name,
                'email' => $collection->email,
                'phone' => $collection->phone
            ];
        }
        return $sibling;
    }

}
