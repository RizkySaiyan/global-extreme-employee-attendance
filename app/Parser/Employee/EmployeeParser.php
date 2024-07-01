<?php

namespace App\Parser\Employee;

use App\Parser\Component\Trait\HasComponentIdName;
use App\Services\Constant\Employee\EmployeeUserRole;
use GlobalXtreme\Parser\BaseParser;
use Illuminate\Support\Facades\Storage;

class EmployeeParser extends BaseParser
{
    /**
     * @param $data
     *
     * @return array|null
     */
    use HasComponentIdName;

    public static function brief($data)
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
            'photo' => Storage::url($data->photo),
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
            'companyOffice' => self::companyOfficeIdName($data->companyOffice),
            'department' => self::departmentIdName($data->department),
        ];
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
            'photo' => Storage::url($data->photo),
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
            'companyOffice' => self::companyOfficeIdName($data->companyOffice),
            'department' => self::departmentIdName($data->department),
            'siblings' => static::siblings($data['siblings'])
        ];
    }

    private static function siblings($collections)
    {

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
