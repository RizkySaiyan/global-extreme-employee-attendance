<?php

namespace App\Algorithms\Attendance;

use App\Models\Attendance\Schedule;
use App\Models\Attendance\Shift;
use App\Models\Attendance\Timesheets;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Attendance\TimesheetConstant;
use App\Services\Constant\Attendance\TimesheetStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TimesheetAlgo
{
    public function __construct(public ?Timesheets $timesheets = null)
    {
    }

    public function attend(): mixed
    {
        $user = Auth::user();
        $now = Carbon::now();

        $schedule = $this->getSchedule($user);
        $startTime = Carbon::parse($schedule->startTime);
        $endTime = Carbon::parse($schedule->endTime);

        if ($endTime->lt($startTime)) {
            $endTime->addDay();
        }

        if ($this->isWithinClockInLimit($startTime, $now)) {
            return $this->clockIn($schedule, $now, $startTime, $user);
        }
        if ($this->isWithinClockOutLimit($startTime, $endTime, $now)) {
            return $this->clockOut($schedule, $now, $endTime, $user);
        }

        errAttendanceTimesheetCannotAttend("Your Schedule start : $startTime");
    }

    private function clockIn($schedule, $now, $startTime, $user)
    {
        try {
            DB::transaction(function () use ($schedule, $startTime, $now, $user) {
                $minuteLate = 0;
                $status = TimesheetStatus::NO_CLOCK_OUT_ID;
                if ($now->gt($startTime)) {
                    $minuteLate = (int)$startTime->diffInMinutes($now);
                    $status = TimesheetStatus::LATE_CLOCK_IN_ID;
                }

                $this->timesheets = $this->checkTimesheetClockIn($user->employeeId, $startTime, $now);
                if ($this->timesheets && $this->timesheets->clockIn != null) {
                    errAttendanceTimesheetAlreadyAttend();
                }

                $this->createTimesheets($schedule, $user, $minuteLate, $now, $status);
            });
            return success($this->timesheets);
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    private function clockOut($schedule, $now, $endTime, $user)
    {
        try {
            DB::transaction(function () use ($schedule, $now, $endTime, $user) {
                $this->timesheets = $this->checkTimesheetClockOut($user->employeeId, $now);

                $minuteEarly = $endTime->gt($now) ? (int)$now->diffInMinutes($endTime) : 0;
                $status = $this->clockOutStatus($now, $endTime);

                if ($this->timesheets) {
                    $this->timesheets->update([
                        'clockOut' => $now,
                        'minuteEarly' => $minuteEarly,
                        'status' => $status
                    ]);
                } else {
                    $status = TimesheetStatus::NO_CLOCK_IN_ID;

                    $this->createTimesheets($schedule, $user, $minuteEarly, $now, $status, true);
                }
            });
            return success($this->timesheets);
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    /** FUNCTIONS */

    private function createTimesheets($schedule, $user, int $minuteValue, Carbon $now, int $status, bool $isClockOut = false)
    {
        $this->timesheets = Timesheets::create([
            'employeeId' => $user->employeeId,
            'shiftId' => $schedule->shiftId ?? $schedule->id,
            'minute' . ($isClockOut ? 'Early' : 'Late') => $minuteValue,
            'clock' . ($isClockOut ? 'Out' : 'In') => $now,
            'status' => $status,
            'createdBy' => $user->employeeId,
            'createdByName' => $user->employee->name
        ]);

        $attend = $isClockOut ? 'clock out' : 'clock in';

        $this->timesheets->setActivityPropertyAttributes(ActivityAction::CREATE)
            ->saveActivity("Attend " . $attend . ": {$this->timesheets->id}, [{$this->timesheets->employee->name}]");
    }

    private function checkTimesheetClockIn($employeeId, $now, $startTime)
    {
        $timesheets = Timesheets::where('employeeId', $employeeId)
            ->whereDate('clockIn', '>=', $startTime->toDateString())
            ->whereDate('clockIn', '<=', $now->toDateString())
            ->latest('clockIn')
            ->first();

        return $timesheets;
    }

    private function checkTimesheetClockOut($employeeId, $now)
    {
        $validateClockOut = Timesheets::where('employeeId', $employeeId)
            ->whereDate('clockOut', $now->toDateString())->exists();

        if ($validateClockOut) {
            errAttendanceTimesheetAlreadyAttend();
        }

        $timesheets = Timesheets::where('employeeId', $employeeId)
            ->whereDate('clockIn', '>=', $now->copy()->subDay())
            ->latest('clockIn')
            ->first();

        return $timesheets;
    }

    private function getSchedule($user): Schedule|Shift
    {
        $schedule = Schedule::where('employeeId', $user->employeeId)
            ->whereDate('date', now()->format('Y-m-d'))
            ->where('reference', Shift::class)->first();

        if (!$schedule) {
            $yesterday = now()->subDay();
            $schedule = Schedule::where('employeeId', $user->employeeId)
                ->whereDate('date', $yesterday)
                ->where('reference', Shift::class)->first();
        }

        return $schedule ? $schedule->scheduleable : Shift::find(TimesheetConstant::DEFAULT_SHIFT_ID);
    }

    private function isWithinClockInLimit(Carbon $startTime, Carbon $now): bool
    {
        return $startTime->diffInMinutes($now) >= TimesheetConstant::CLOCK_IN_START
            && $startTime->diffInMinutes($now) <= TimesheetConstant::CLOCK_IN_LIMIT;
    }

    private function isWithinClockOutLimit(Carbon $startTime, Carbon $endTime, Carbon $now): bool
    {
        return $endTime->diffInMinutes($now) <= TimesheetConstant::CLOCK_OUT_LIMIT
            && $startTime->diffInMinutes($now) > TimesheetConstant::CLOCK_IN_LIMIT;
    }

    private function clockOutStatus(Carbon $now, Carbon $endTime): int
    {
        if ($endTime->gt($now)) {
            return TimesheetStatus::EARLY_CLOCK_OUT_ID;
        }
        if ($this->timesheets && $this->timesheets->status == TimesheetStatus::LATE_CLOCK_IN_ID) {
            return TimesheetStatus::LATE_CLOCK_IN_ID;
        }

        return TimesheetStatus::VALID_ID;
    }
}
