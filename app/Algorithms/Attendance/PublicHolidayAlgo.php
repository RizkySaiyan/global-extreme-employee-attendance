<?php

namespace App\Algorithms\Attendance;

use App\Http\Requests\Attendance\PublicHolidayRequest;
use App\Jobs\Attendance\AssignPublicHolidayJob;
use App\Models\Attendance\PublicHoliday;
use App\Services\Constant\Activity\ActivityAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PublicHolidayAlgo
{
    public function __construct(public ?PublicHoliday $publicHoliday = null)
    {
    }

    public function create(PublicHolidayRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $user = Auth::user();

                $createdBy = [
                    'createdBy' => $user->employeeId,
                    'createdByName' => $user->employee->name
                ];

                $this->checkDate($request);

                $this->publicHoliday = PublicHoliday::create($request->only('name', 'date') + $createdBy);

                $this->publicHoliday->setActivityPropertyAttributes(ActivityAction::CREATE)
                    ->saveActivity("Enter new public holiday : {$this->publicHoliday->id}, [{$this->publicHoliday->name}]");
            });
            return success($this->publicHoliday);
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    public function update(PublicHolidayRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $this->publicHoliday->setOldActivityPropertyAttributes(ActivityAction::UPDATE);

                $this->checkDate($request);

                $this->publicHoliday->update($request->all());

                $this->publicHoliday->setActivityPropertyAttributes(ActivityAction::UPDATE)
                    ->saveActivity("Update public holiday : {$this->publicHoliday->id}, [{$this->publicHoliday->name}]");
            });
            return success($this->publicHoliday->fresh());
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    public function delete()
    {
        try {
            DB::transaction(function () {
                $this->publicHoliday->setOldActivityPropertyAttributes(ActivityAction::DELETE);

                $this->publicHoliday->delete();

                $this->publicHoliday->setActivityPropertyAttributes(ActivityAction::DELETE)
                    ->saveActivity("Delete public holiday : {$this->publicHoliday->id}, [{$this->publicHoliday->name}]");
            });
            return success($this->publicHoliday->fresh());
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    public function assignPublicHoliday()
    {
        try {
            DB::transaction(function () {
                $user = Auth::user();

                $this->publicHoliday->setOldActivityPropertyAttributes(ActivityAction::UPDATE);

                if ($this->publicHoliday->isAssigned == 1) {
                    errPublicHolidayAssigned();
                }
                AssignPublicHolidayJob::dispatch($this->publicHoliday, $user);

                $this->publicHoliday->setActivityPropertyAttributes(ActivityAction::UPDATE)
                    ->saveActivity("Assign public holiday to employe : {$this->publicHoliday->name}, [{$this->publicHoliday->date}]");
            });
            return success($this->publicHoliday);
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    /** FUNCTION */

    public function checkDate($request)
    {
        $query = PublicHoliday::whereDate('date', $request->date);
        if ($this->publicHoliday) {
            $query->where('id', '!=', $this->publicHoliday->id);
        }
        $dates = $query->first();

        if ($dates) {
            errAttendanceDateExist();
        }
    }
}
