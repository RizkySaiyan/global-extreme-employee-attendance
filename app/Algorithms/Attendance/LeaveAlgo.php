<?php

namespace App\Algorithms\Attendance;

use App\Models\Attendance\Leave;
use App\Models\Attendance\Traits\SaveSchedule;
use App\Models\Employee\Employee;
use App\Parser\Attendance\LeaveParser;
use App\Services\Constant\Attendance\LeaveBalance;
use App\Services\Constant\Attendance\StatusType;
use App\Services\Constant\Employee\EmployeeUserRole;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaveAlgo
{
    use SaveSchedule;

    public function __construct(public ?Leave $leave = null)
    {
    }

    public function create($request)
    {
        try {
            DB::transaction(function () use ($request) {
                $user = Auth::user();

                $this->dateValidation($request, $user);
                $leaves = Leave::getEmployeeLeaves($user->employeeId);

                if ($leaves == 12) {
                    errLeaveBalance();
                }

                $this->leave = $this->saveLeave($request, $user);

                if ($user->role == EmployeeUserRole::ADMIN_ID) {
                    self::saveSchedule($this->leave, $this->leave->toArray());
                }
            });
            return success($this->leave);
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    public function approveLeaves()
    {
        try {
            DB::transaction(function () {
                $user = Auth::user();

                $this->leave->update([
                    'status' => StatusType::APPROVED_ID,
                    'approvedBy' => $user->employeeId,
                    'approvedByName' => $user->employee->name
                ]);
                self::saveSchedule($this->leave, $this->leave->toArray());
            });
            return success($this->leave);
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    /** FUNCTION */
    public function saveLeave($request, $user)
    {
        $form = $request->all();

        $form['employeeId'] = $user->employeeId;
        $form['status'] = StatusType::PENDING_ID;

        $createdBy = [
            'createdBy' => $user->employeeId,
            'createdByName' => $user->employee->name
        ];

        if ($user->role == EmployeeUserRole::ADMIN_ID) {
            $form['approvedBy'] = $user->employeeId;
            $form['approvedByName'] = $user->employee->name;
            $form['status'] = StatusType::APPROVED_ID;
        }

        $leave = Leave::create($form + $createdBy);

        return $leave;
    }

    // private function employeeBalance($user)
    // {
    //     $year = now()->year;

    //     $leave = Leave::where('employeeId', $user)
    //         ->whereNotNull('approvedBy')
    //         ->whereYear('fromDate', $year)
    //         ->whereYear('toDate', $year)
    //         ->pluck('toDate', 'fromDate');

    //     $totalLeaves = 0;
    //     $leave->each(function ($endDate, $startDate) use (&$totalLeaves) {
    //         $start = Carbon::parse($startDate);
    //         $end = Carbon::parse($endDate);

    //         $days = $start->diffInDays($end);
    //         $totalLeaves += $days;
    //     });

    //     return $totalLeaves;
    // }

    private function dateValidation($request, $user)
    {
        $fromDate = Carbon::createFromDate($request->fromDate);
        $toDate = Carbon::createFromDate($request->toDate);

        $count = $fromDate->diffInDays($toDate);
        if ($count >= Carbon::DAYS_PER_WEEK) {
            errLeaveMoreThanAWeek();
        }

        $leaves = Leave::where('employeeId', $user->employeeId)
            ->whereDate('fromDate', '<=', $request->toDate)
            ->whereDate('toDate', '>=', $request->fromDate)->first();

        if ($leaves) {
            errLeaveExist("Assigned dates, [fromDate : $leaves->fromDate, toDate : $leaves->toDate]");
        }
    }
}
