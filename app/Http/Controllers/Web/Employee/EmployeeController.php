<?php

namespace App\Http\Controllers\Web\Employee;

use App\Algorithms\Employee\EmployeeAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeRequest;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    //

    public function create(EmployeeRequest $request){
        $algo =  new EmployeeAlgo();

        return $algo->create($request);
    }
}
