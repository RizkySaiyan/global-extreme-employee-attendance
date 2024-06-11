<?php

namespace App\Algorithms\Component;

use App\Models\Component\CompanyOffice;
use App\Models\Component\CompanyOfficeDepartment;
use App\Models\Component\Department;
use App\Parser\Component\CompanyOfficeParser;
use App\Parser\Employee\EmployeeParser;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyOfficeAlgo
{

    public function saveDepartment($model, Request $request)
    {

        try {
            $mapDepartment = DB::transaction(function () use ($model, $request) {
                foreach ($request->departmentIds as $departmentId) {
                    $map = CompanyOfficeDepartment::updateOrCreate(['companyOfficeId' => $model->id, 'departmentId' => $departmentId], 
                    ['companyOfficeId' => $model->id, 'departmentId' => $departmentId]);
                }
                $map->setActivityPropertyAttributes(ActivityAction::CREATE)
                ->saveActivity("Mapping company office with department $map->id, companyId : [$map->companyOfficeId]");

                return $map;
            });

            return success($mapDepartment);
        } catch (Exception $exception) {
            exception($exception);
        }
    }

    public function departmentMappings($model)
    {
        try {
            $companyOfficeDepartment = $model->officeDepartments;

            return success(CompanyOfficeParser::departmentMap($companyOfficeDepartment));
        } catch (Exception $exception) {
            exception($exception);
        }
    }
}
