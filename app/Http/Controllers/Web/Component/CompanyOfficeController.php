<?php

namespace App\Http\Controllers\Web\Component;

use App\Algorithms\Component\CompanyOfficeAlgo;
use App\Algorithms\Component\ComponentAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Component\CompanyOfficeDepartmentRequest;
use App\Http\Requests\Component\CompanyOfficeRequest;
use App\Models\Component\CompanyOffice;
use App\Parser\Component\CompanyOfficeParser;
use Illuminate\Http\Request;

class CompanyOfficeController extends Controller
{
    //
    public function get(Request $request)
    {
        $companyOffice = CompanyOffice::getOrPaginate($request);

        return success($companyOffice);
    }

    public function create(CompanyOfficeRequest $request)
    {
        $algo = new ComponentAlgo();

        return $algo->createBy(CompanyOffice::class, $request);
    }

    public function update($id, CompanyOfficeRequest $request)
    {
        $companyOffice = CompanyOffice::find($id);
        if (!$companyOffice) {
            errCompanyOfficeNotFound();
        }

        $algo = new ComponentAlgo();

        return $algo->update($companyOffice, $request);
    }

    public function delete($id)
    {
        $companyOffice = CompanyOffice::find($id);
        if (!$companyOffice) {
            errCompanyOfficeNotFound();
        }

        $algo = new ComponentAlgo();

        return $algo->delete($companyOffice);
    }

    public function saveDepartment($id, CompanyOfficeDepartmentRequest $request)
    {
        $companyOffice = CompanyOffice::find($id);
        if (!$companyOffice) {
            errCompanyOfficeNotFound();
        }

        $algo = new CompanyOfficeAlgo();

        return $algo->saveDepartment($companyOffice, $request);
    }

    public function getDepartmentMapping($id)
    {
        $companyOffice = CompanyOffice::find($id);
        if (!$companyOffice) {
            errCompanyOfficeNotFound();
        }

        $algo = new CompanyOfficeAlgo();
        return $algo->departmentMappings($companyOffice);
    }
}
