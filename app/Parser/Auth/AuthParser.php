<?php

namespace App\Parser\Auth;

use App\Services\Constant\Employee\EmployeeUserRole;
use GlobalXtreme\Parser\BaseParser;

class AuthParser extends BaseParser
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

        return parent::first($data);
    }

    public static function getAuthenticatedUser($data){

        if(!$data){
            return null;
        }

        return [
            'id' => $data->id,
            'employeeId' => $data->employeeId,
            'name' => $data->employee->name,
            'email' => $data->email,
            'photo' => $data->employee->photo,
            'role' => EmployeeUserRole::display($data->role),
            'isResign' => $data->employee->isResign,
        ];
    }
}
