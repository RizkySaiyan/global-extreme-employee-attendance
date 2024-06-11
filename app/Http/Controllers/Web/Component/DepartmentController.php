<?php

namespace App\Http\Controllers\Web\Component;

use App\Algorithms\Component\ComponentAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Component\ComponentRequest;
use App\Models\Component\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function get(Request $request)
    {
        $department = Department::getOrPaginate($request);

        return success($department);
    }

    public function create(ComponentRequest $request)
    {

        $algo = new ComponentAlgo();

        return $algo->createBy(Department::class, $request);
    }

    public function update($id, ComponentRequest $request)
    {

        $department = Department::find($id);

        if (!$department) {
            errDepartmentNotFound();
        }

        $algo = new ComponentAlgo();

        return $algo->update($department, $request);
    }

    public function delete($id)
    {

        $department = Department::find($id);

        if (!$department) {
            errDepartmentNotFound();
        }

        $algo = new ComponentAlgo();

        return $algo->delete($department);
    }
}
