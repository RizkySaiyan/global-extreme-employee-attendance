<?php

namespace App\Algorithms\Attendance;

use App\Http\Requests\Attendance\ShiftRequest;
use App\Models\Attendance\Shift;
use App\Services\Constant\Activity\ActivityAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShiftAlgo
{
    public function __construct(public ?Shift $shift = null)
    {
    }

    public function create(ShiftRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $user = Auth::user();

                $createdBy = [
                    'createdBy' => $user->employeeId,
                    'createdByName' => $user->employee->name
                ];

                $this->shift = Shift::create($request->only('name', 'startTime', 'endTime') + $createdBy);

                $this->shift->setActivityPropertyAttributes(ActivityAction::CREATE)
                    ->saveActivity("Enter new Shifts : {$this->shift->name}, [{$this->shift->id}]");
            });
            return success($this->shift);
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    public function update(ShiftRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $this->shift->setOldActivityPropertyAttributes(ActivityAction::UPDATE);

                $this->shift->update($request->only('name', 'startTime', 'endTime'));

                $this->shift->setActivityPropertyAttributes(ActivityAction::UPDATE)
                    ->saveActivity("Update shift {$this->shift->id}, [{$this->shift->name}]");
            });
            return success($this->shift->fresh());
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    public function delete()
    {
        try {
            DB::transaction(function () {
                $this->shift->setOldActivityPropertyAttributes(ActivityAction::DELETE);

                $this->shift->delete();

                $this->shift->setActivityPropertyAttributes(ActivityAction::DELETE)
                    ->saveActivity("Delete shift {$this->shift->id}, [{$this->shift->name}]");
            });
            return success();
        } catch (\Exception $exception) {
            exception($exception);
        }
    }
}
