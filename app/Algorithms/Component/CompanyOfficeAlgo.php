<?php

namespace App\Algorithms\Component;

use App\Models\Component\CompanyOffice;
use App\Models\Component\CompanyOfficeDepartment;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyOfficeAlgo{

    public function saveDepartment($model, Request $request){

        try{
            $mapDepartment = DB::transaction(function() use($model, $request){

                //check CompanyOffice has department
                $checkDepartment = CompanyOfficeDepartment::where('companyOfficeId',$model->id)
                ->where('departmentId',$request->departmentId);

                if($checkDepartment->exists()){
                    errCompanyOfficeHasDepartment();
                }

                $map = CompanyOfficeDepartment::create([
                    'companyOfficeId' => $model->id,
                    'departmentId' =>$request->departmentId
                ]);

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
}