<?php

namespace App\Algorithms\Attendance;

use App\Models\Attendance\PublicHoliday;
use App\Models\Attendance\Schedule;
use App\Models\Attendance\Shift;
use App\Models\Attendance\Traits\SaveSchedule;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Attendance\AttendanceType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScheduleAlgo
{
    public function __construct(public ?Schedule $schedule = null)
    {
    }

    public function create($request)
    {
        try {

            DB::transaction(function () use ($request) {
                $schedule = Schedule::where('date', $request->date);

                if ($schedule->exists()) {
                    errAttendanceDateExist();
                }

                $this->schedule = $this->assignSchedule($request);

                $this->schedule->setActivityPropertyAttributes(ActivityAction::CREATE)
                    ->saveActivity("Enter new schedule : {$this->schedule->id}, [{$this->schedule->date}]");
            });
            return success($this->schedule);
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    public function delete()
    {
        try {
            DB::transaction(function () {
                $this->schedule->setOldActivityPropertyAttributes(ActivityAction::DELETE);

                $this->schedule->delete();

                $this->schedule->setActivityPropertyAttributes(ActivityAction::DELETE)
                    ->saveActivity("Delete schedule {$this->schedule->id}, [{$this->schedule->date}]");
            });
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    /** FUNCTION */
    public function assignSchedule($request)
    {
        $scheduleable = null;

        switch ($request->type) {
            case AttendanceType::SHIFT_ID:
                $scheduleable = Shift::find($request->reference);
                if (!$scheduleable) {
                    errShiftNotFound();
                }
                break;

            case AttendanceType::PUBLIC_HOLIDAY_ID:
                $scheduleable = PublicHoliday::find($request->reference);
                if (!$scheduleable) {
                    errPublicHolidayNotFound();
                }
                break;
            default:
                errAttendanceTypeNotFound();
                break;
        }
        return Schedule::saveSchedule($scheduleable, $request);
    }
}
