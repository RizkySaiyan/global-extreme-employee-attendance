<?php

namespace App\Http\Controllers\Web\Attendance;

use App\Algorithms\Attendance\LeaveAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\LeaveRequest;
use App\Models\Attendance\Leave;
use App\Parser\Attendance\LeaveParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    //
    public function get(Request $request)
    {
        $leave = Leave::filter($request)->getOrPaginate($request);
        return success(LeaveParser::getLeaves($leave), pagination: pagination($leave));
    }

    public function personalLeaves(Request $request)
    {
        $user = Auth::user();

        $leave = Leave::where('employeeId', $user->employeeId)->get();

        return success(LeaveParser::getLeaves($leave));
    }

    public function create(LeaveRequest $request)
    {
        $algo = new LeaveAlgo();
        return $algo->create($request);
    }

    public function delete($id)
    {
        $leave = Leave::find($id);
        if (!$leave) {
            errLeaveNotFound();
        }

        $algo = new LeaveAlgo($leave);
        return $algo->delete();
    }

    public function approveLeaves($id)
    {
        $leave = Leave::find($id);
        if (!$leave) {
            errLeaveNotFound();
        }

        $algo = new LeaveAlgo($leave);
        return $algo->approveLeaves();
    }

    public function checkBalance()
    {
        $user = Auth::user();
        return success(LeaveParser::balance($user));
    }
}
