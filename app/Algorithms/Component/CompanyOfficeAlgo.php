<?php

namespace App\Algorithms\Component;

use App\Models\Component\CompanyOffice;
use App\Models\Component\CompanyOfficeDepartment;
use App\Models\Component\Department;
use App\Parser\Component\CompanyOfficeParser;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyOfficeAlgo{

    public function saveDepartment($model, Request $request){

        try{
            $mapDepartment = DB::transaction(function() use($model, $request){
                foreach($request->departmentIds as $departmentId){
                    $map = CompanyOfficeDepartment::updateOrCreate([
                        'companyOfficeId' =>$model->id,
                        'departmentId' => $departmentId
                    ],[
                        'companyOfficeId' => $model->id,
                        'departmentId' =>$departmentId
                    ]);
                }
                $map->setActivityPropertyAttributes(ActivityAction::CREATE)
                ->saveActivity("Mapping company office with department $map->id, companyId : [$map->companyOfficeId]");
    
                return $map;
            });

            return success($mapDepartment);
        }
        catch(\Exception $exception){
            exception($exception);
        }
    }

    public function departmentMappings($model){
        try {
            $departments = Department::all();
            $existingIds = CompanyOfficeDepartment::where('companyOfficeId',$model->id)->pluck('departmentId')->toArray();
            $map = $departments->map(function ($department) use ($existingIds) {
                return [
                    'assigned' => in_array($department->id, $existingIds),
                    'id' => $department->id,
                    'name' => $department->name
                ];
            });

            return success(CompanyOfficeParser::departmentMap($map));
        } catch (\Exception $exception) {
            exception($exception);
        }

    }
}