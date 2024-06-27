<?php

namespace App\Algorithms\Attendance;

use App\Models\Attendance\Schedule;
use App\Models\Attendance\Shift;
use App\Models\Attendance\Timesheets;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Attendance\TimesheetConstant;
use App\Services\Constant\Attendance\TimesheetStatus;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
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

        $isNightShift = false;
        if ($endTime->lt($startTime)) {
            $endTime->addDay();
            $isNightShift = true;
        }

        if ($endTime->lt($startTime)) {
            $endTime->addDay();
        }
        if ($this->isWithinClockInLimit($startTime, $now)) {
            return $this->clockIn($schedule, $now, $startTime, $user, $isNightShift);
        }
        if ($this->isWithinClockOutLimit($startTime, $endTime, $now)) {
            return $this->clockOut($schedule, $now, $endTime, $user, $isNightShift);
        }

        errAttendanceTimesheetCannotAttend("Your Schedule start : $startTime");
    }

    private function clockIn($schedule, $now, $startTime, $user, $isNightShift): JsonResponse
    {
        try {
            DB::transaction(function () use ($schedule, $startTime, $now, $user, $isNightShift) {
                $minuteLate = 0;
                $status = TimesheetStatus::NO_CLOCK_OUT_ID;
                if ($now->gt($startTime)) {
                    $minuteLate = (int)$startTime->diffInMinutes($now);
                    $status = TimesheetStatus::LATE_CLOCK_IN_ID;
                }

                $this->timesheets = $this->getTimesheet($user->employeeId, $now, $isNightShift);
                if ($this->timesheets) {
                    errAttendanceTimesheetAlreadyAttend();
                }

                $this->createTimesheets($schedule, $user, $minuteLate, $now, $status);
            });
            return success($this->timesheets);
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    private function clockOut($schedule, $now, $endTime, $user, $isNightShift): JsonResponse
    {
        try {
            DB::transaction(function () use ($schedule, $now, $endTime, $user, $isNightShift) {

                $minuteEarly = $endTime->gt($now) ? (int)$now->diffInMinutes($endTime) : 0;
                $status = $this->clockOutStatus($now, $endTime);

                $this->timesheets = $this->getTimesheet($user->employeeId, $now, $isNightShift);
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



    private function getTimesheet($employeeId, Carbon $now, $isNightShift = false): Timesheets|null
    {
        $yesterday = $now->copy()->subDay();
        $query = Timesheets::where('employeeId', $employeeId)
            ->where(function ($query) use ($now, $yesterday, $isNightShift) {
                if ($isNightShift) {
                    $query->whereBetween('clockIn', [$yesterday->toDateString(), $now->toDateString()])
                        ->whereNull('clockOut')
                        ->where(function ($query) use ($now) {
                            $query->whereRaw('TIMESTAMPDIFF(HOUR, clockIn, ?) <= ?', [$now, TimesheetConstant::WORK_HOURS])
                                ->orWhereDate('clockIn', $now->toDateString())
                                ->orWhere(function ($query) use ($now) {
                                    $query->whereRaw('TIMESTAMPDIFF(HOUR, clockOut, ?) <= ?', [$now, TimesheetConstant::WORK_HOURS])
                                        ->orWhereDate('clockOut', $now->toDateString());
                                });
                        });
                } else {
                    // Check for normal day shifts
                    $query->where(function ($subQuery) use ($now) {
                        $subQuery->whereDate('clockIn', $now->toDateString())
                            ->whereNull('clockOut');
                    })
                        ->orWhere(function ($subQuery) use ($now) {
                            $subQuery->whereDate('clockOut', $now->toDateString());
                        });
                }
            });

        return $query->exists() ? $query->latest()->first() : null;
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
