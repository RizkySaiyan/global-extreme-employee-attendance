<?php

namespace App\Algorithms\Attendance;

use App\Http\Requests\Attendance\CorrectionRequest;
use App\Models\Attendance\Correction;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Attendance\StatusType;
use App\Services\Constant\Attendance\TimesheetStatus;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CorrectionAlgo
{
    public function __construct(public ?Correction $corrections = null)
    {
    }

    public function create(CorrectionRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $user = Auth::user();
                $employeeId = $request->has('employeeId') ? $request->employeeId : $user->employeeId;

                $this->corrections = Correction::create([
                    'employeeId' => $employeeId,
                    'date' => $request->date,
                    'notes' => $request->notes,
                    'clockIn' => $request->clockIn,
                    'clockOut' => $request->clockOut,
                    'timesheetId' => $request->timesheetId,
                    'status' => StatusType::PENDING_ID,
                    'createdBy' => $user->employeeId,
                    'createdByName' => $user->employee->name
                ]);

                $this->corrections->setActivityPropertyAttributes(ActivityAction::CREATE)
                    ->saveActivity("Enter new correction : {$this->corrections->id}, [{$this->corrections->notes}]");
            });
            return success($this->corrections);
        } catch (Exception $exception) {
            exception($exception);
        }
    }

    public function approves()
    {
        try {

            DB::transaction(function () {
                $user = Auth::user();

                $this->corrections->setOldActivityPropertyAttributes(ActivityAction::UPDATE);

                if ($this->corrections->status == StatusType::APPROVED_ID || $this->corrections->status == StatusType::DISAPPROVES_ID) {
                    errAttendanceCorrectionAssessment();
                }

                $this->corrections->update([
                    'approvedBy' => $user->employeeId,
                    'approvedByName' => $user->employee->name,
                    'status' => StatusType::APPROVED_ID
                ]);

                $date = $this->corrections->date;
                $clockInTime = $this->corrections->clockIn;
                $clockOutTime = $this->corrections->clockOut;

                $clockIn = Carbon::createFromFormat('Y-m-d H:i:s', "{$date} {$clockInTime}");
                $clockOut = Carbon::createFromFormat('Y-m-d H:i:s', "{$date} {$clockOutTime}");

                if ($clockOut->lt($clockIn)) {
                    $clockOut->addDay();
                }

                $this->corrections->timesheet()->create([
                    'shiftId' => $this->corrections->timesheet->shiftId,
                    'employeeId' => $this->corrections->employeeId,
                    'clockIn' => $clockIn,
                    'clockOut' => $clockOut,
                    'status' => TimesheetStatus::VALID_ID,
                ]);

                $this->corrections->setActivityPropertyAttributes(ActivityAction::UPDATE)
                    ->saveActivity("Approves correction : {$this->corrections->id}, [{$this->corrections->status}]");
            });
            return success($this->corrections->fresh());
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    public function disapproves()
    {
        try {

            DB::transaction(function () {
                $user = Auth::user();

                $this->corrections->setOldActivityPropertyAttributes(ActivityAction::UPDATE);

                $this->corrections->update([
                    'approvedBy' => $user->employeeId,
                    'approvedByName' => $user->employee->name,
                    'status' => StatusType::DISAPPROVES_ID
                ]);

                $this->corrections->setActivityPropertyAttributes(ActivityAction::UPDATE)
                    ->saveActivity("Disapproves correction : {$this->corrections->id}, [{$this->corrections->status}]");
            });
            return success($this->corrections->fresh());
        } catch (\Exception $exception) {
            exception($exception);
        }
    }
}
