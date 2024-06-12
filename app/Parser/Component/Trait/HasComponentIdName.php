<?php

namespace App\Parser\Component\Trait;

trait HasComponentIdName
{
    //
    public static function companyOfficeIdName($data)
    {
        return [
            'id' => $data->id,
            'name' => $data->name,
        ];
    }

    public static function departmentIdName($data)
    {
        return [
            'id' => $data->id,
            'name' => $data->name,
        ];
    }
}
