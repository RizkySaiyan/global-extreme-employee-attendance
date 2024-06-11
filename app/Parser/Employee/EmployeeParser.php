<?php

namespace App\Parser\Employee;

use App\Services\Constant\Employee\EmployeeUserRole;
use GlobalXtreme\Parser\BaseParser;

class EmployeeParser extends BaseParser
{
    /**
     * @param $data
     *
     * @return array|null
     */

    public static function get($collections)
    {
        if(!$collections || count($collections) == 0){
            return null;
        }

        $data = [];
        foreach ($collections as $collection) {
            $data[] = [
                'id' => $collection->id,
                'name' => $collection->name,
                'number' => $collection->number,
                'phone' => $collection->phone,
                'address' => $collection->address,
                'photo' => $collection->photo,
                'email' => $collection?->user?->email,
                'fatherName' => $collection->fatherName,
                'fatherEmail' => $collection->fatherEmail,
                'fatherPhone' => $collection->fatherPhone,
                'motherName' => $collection->motherName,
                'motherEmail' => $collection->motherEmail,
                'motherPhone' => $collection->motherPhone,
                'isResign' => $collection->isResign,
                'role' => EmployeeUserRole::display($collection?->user?->role),
                'createdBy' => $collection->createdBy,
                'createdByName' => $collection->createdByName,
            ];
        }
        return $data;
    }

    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'name' => $data->name,
            'number' => $data->number,
            'phone' => $data->phone,
            'address' => $data->address,
            'photo' => $data->photo,
            'email' => $data?->user?->email,
            'fatherName' => $data->fatherName,
            'fatherEmail' => $data->fatherEmail,
            'fatherPhone' => $data->fatherPhone,
            'motherName' => $data->motherName,
            'motherEmail' => $data->motherEmail,
            'motherPhone' => $data->motherPhone,
            'isResign' => $data->isResign,
            'role' => EmployeeUserRole::display($data?->user?->role),
            'createdBy' => $data->createdBy,
            'createdByName' => $data->createdByName,
            'siblings' => static::siblings($data['siblings'])
        ]; 
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
