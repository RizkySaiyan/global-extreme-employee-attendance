<?php

namespace App\Algorithms\Component;

use App\Models\Component\CompanyOfficeDepartment;
use App\Models\Component\Department;
use App\Parser\Component\CompanyOfficeParser;
use App\Services\Constant\Activity\ActivityAction;
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
                    $map = CompanyOfficeDepartment::updateOrCreate(
                        ['companyOfficeId' => $model->id, 'departmentId' => $departmentId],
                        ['companyOfficeId' => $model->id, 'departmentId' => $departmentId]
                    );
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
            $departments = Department::all();

            $existingIds = $companyOfficeDepartment->pluck('id')->toArray();
            $data = $departments->map(function ($department) use ($existingIds) {
                return [
                    'assigned' => !empty($existingIds) && in_array($department->id, $existingIds),
                    'id' => $department->id,
                    'name' => $department->name
                ];
            })->toArray();

            return success($data);
        } catch (Exception $exception) {
            exception($exception);
        }
    }
}
