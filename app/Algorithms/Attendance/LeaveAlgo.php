<?php

namespace App\Algorithms\Attendance;

use App\Models\Attendance\Leave;
use App\Models\Attendance\Schedule;
use App\Models\Attendance\Traits\SaveSchedule;
use App\Models\Employee\Employee;
use App\Parser\Attendance\LeaveParser;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Attendance\LeaveBalance;
use App\Services\Constant\Attendance\LeaveConstant;
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

                $this->dateValidation($request);
                $totalLeaves = Leave::getEmployeeLeaves($request->employeeId);

                if ($totalLeaves == LeaveConstant::LEAVE_BALANCE) {
                    errLeaveBalance();
                }

                $this->saveLeave($request, $user);
                if (!$this->leave) {
                    errLeaveSave();
                }

                $this->leave->setActivityPropertyAttributes(ActivityAction::CREATE)
                    ->saveActivity("Enter new Leaves : {$this->leave->id}, [{$this->leave->name}]");

                if ($user->role == EmployeeUserRole::ADMIN_ID) {
                    Schedule::saveSchedule($this->leave, $this->leave->toArray());
                }
            });
            return success($this->leave);
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    public function delete()
    {
        try {

            DB::transaction(function () {
                $this->leave->setOldActivityPropertyAttributes(ActivityAction::DELETE);

                if ($this->leave->createdAt->diffInMonths(now()) >= 1) {
                    errLeaveDelete();
                }

                $this->leave->delete();
                $this->leave->schedule()->delete();

                $this->leave->setActivityPropertyAttributes(ActivityAction::DELETE)
                    ->saveActivity("Delete leaves : {$this->leave->id} [{$this->leave->employeeId}]");
            });
            return success();
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
                Schedule::saveSchedule($this->leave, $this->leave->toArray());
            });
            return success($this->leave);
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    /** FUNCTION */
    private function saveLeave($request, $user)
    {
        $form = $request->all();

        $form['employeeId'] = $request->employeeId;
        $form['status'] = StatusType::PENDING_ID;

        $createdBy = [
            'createdBy' => $request->employeeId,
            'createdByName' => $request->employee->name
        ];

        if ($user->role == EmployeerequestRole::ADMIN_ID) {
            $form['approvedBy'] = $request->employeeId;
            $form['approvedByName'] = $request->employee->name;
            $form['status'] = StatusType::APPROVED_ID;
        }
        $this->leave = Leave::create($form + $createdBy);
    }

    private function dateValidation($request)
    {
        $fromDate = Carbon::createFromDate($request->fromDate);
        $toDate = Carbon::createFromDate($request->toDate);

        $count = $fromDate->diffInDays($toDate);
        if ($count >= LeaveConstant::LEAVE_REQUEST_LIMIT) {
            errLeaveMoreThanAWeek();
        }

        $leaves = Leave::where('employeeId', $request->employeeId)
            ->whereDate('fromDate', '<=', $request->toDate)
            ->whereDate('toDate', '>=', $request->fromDate)->first();
        if ($leaves) {
            errLeaveExist("Assigned dates, [fromDate : $leaves->fromDate, toDate : $leaves->toDate]");
        }
    }
}
