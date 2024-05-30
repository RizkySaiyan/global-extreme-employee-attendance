<?php

namespace App\Http\Controllers\Web\Component;

use App\Algorithms\Component\CompanyOfficeAlgo;
use App\Algorithms\Component\ComponentAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Component\ComponentRequest;
use App\Models\Component\CompanyOffice;
use App\Models\Component\CompanyOfficeDepartment;
use App\Parser\Component\CompanyOfficeParser;
use Illuminate\Http\Request;

class CompanyOfficeController extends Controller
{
    //
    public function get(Request $request){
        $companyOffice = CompanyOffice::getOrPaginate($request);

        return success($companyOffice);
    }


    public function create(ComponentRequest $request){
        
        $algo = new ComponentAlgo();

        return $algo->createBy(CompanyOffice::class, $request);
    }

    public function update($id,ComponentRequest $request){
        
        $companyOffice = CompanyOffice::find($id);

        if(!$companyOffice){
            errCompanyOfficeNotFound();
        }

        $algo = new ComponentAlgo();

        return $algo->update($companyOffice, $request);
    }


    public function delete($id){

        $companyOffice = CompanyOffice::find($id);

        if(!$companyOffice){
            errCompanyOfficeNotFound();
        }

        $algo = new ComponentAlgo();

        return $algo->delete($companyOffice);
    }

    public function saveDepartment($id, Request $request){
        $companyOffice = CompanyOffice::find($id);

        if(!$companyOffice){
            errCompanyOfficeNotFound();
        }

        $algo = new CompanyOfficeAlgo();

        return $algo->saveDepartment($companyOffice, $request);
    }

    public function getDepartmentMapping($id){
        $companyOfficeDepartment = CompanyOffice::find($id);
        
        if (!$companyOfficeDepartment) {
            errCompanyOfficeNotFound();
        }

        return success(CompanyOfficeParser::first($companyOfficeDepartment));
    }
}
