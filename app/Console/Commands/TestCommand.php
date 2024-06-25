<?php

namespace App\Console\Commands;

use App\Models\Attendance\Leave;
use App\Models\Attendance\Schedule;
use App\Models\Attendance\Shift;
use App\Models\Attendance\Timesheets;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeUser;
use App\Services\Constant\Attendance\AttendanceType;
use App\Services\Constant\Attendance\TimesheetConstant;
use App\Services\Constant\Attendance\TimesheetStatus;
use App\Services\Number\Generator\EmployeeNumber;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestCommand extends Command
{
    protected $signature = 'dev-test';
    protected $description = '';

    public function handle()
    {
        // $sundays = [];
        // $date = Carbon::create(now()->year, 1, 1);

        // if ($date->dayOfWeek != Carbon::SUNDAY) {
        //     $date->modify('next sunday');
        // }

        // while ($date->year == now()->year) {
        //     $sundays[] = $date->format('Y-m-d');
        //     $date->addWeek();
        // }
        // $employees = Employee::whereDoesntHave('schedules', function ($query) {
        //     $query->where('type', AttendanceType::WEEKLY_DAY_OFF_ID);
        // })->get();
        // foreach ($employees as $employee) {
        //     foreach ($sundays as $sunday) {
        //         Schedule::create([
        //             'employeeId' => $employee->id,
        //             'date' => $sunday,
        //             'type' => AttendanceType::WEEKLY_DAY_OFF_ID,
        //             'referenceId' => null,
        //             'reference' => null,
        //             'createdBy' => 0,
        //             'createdBy' => 'System'
        //         ]);
        //     }
        // }

        // $year = now()->year;

        // $leave = Leave::where('employeeId', 1)
        //     ->whereNotNull('approvedBy')
        //     ->whereYear('fromDate', $year)
        //     ->whereYear('toDate', $year)
        //     ->pluck('toDate', 'fromDate');


        // $totalLeaves = 0;
        // $leave->each(function ($endDate, $startDate) use (&$totalLeaves) {
        //     $start = Carbon::parse($startDate);
        //     $end = Carbon::parse($endDate);

        //     $days = $start->diffInDays($end);
        //     $totalLeaves += $days;
        // });

        // $schedule = Schedule::where('employeeId', 1)
        //     ->whereDate('date', '2024-06-21')
        //     ->where('reference', Shift::class)->first();

        // $schedule = $schedule?->scheduleable;
        // if (!$schedule) {
        //     $schedule = Shift::find(TimesheetConstant::DEFAULT_SHIFT_ID);
        // }

        $user = EmployeeUser::find(3);
        $now = Carbon::create(2024, 6, 29, 22, 56);

        $schedule = $this->getSchedule($user);
        $startTime = Carbon::parse($schedule->startTime)->addDays(5);
        $endTime = Carbon::parse($schedule->endTime)->addDays(5);
        $isNightShift = false;
        if ($endTime->lt($startTime)) {
            $endTime->addDay();
            $isNightShift = true;
        }
        // dd($schedule, $startTime, $endTime);
        if ($this->isWithinClockInLimit($startTime, $now)) {
            return $this->clockIn($schedule, $now, $startTime, $user, $isNightShift);
        }
        if ($this->isWithinClockOutLimit($startTime, $endTime, $now)) {
            return $this->clockOut($schedule, $now, $endTime, $user, $isNightShift);
        }
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

    private function clockIn($schedule, $now, $startTime, $user, $isNightShift)
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
                // dd($this->timesheets, $isNightShift);
                if (!$this->timesheets) {
                    $this->createTimesheets($schedule, $user, $minuteLate, $now, $status);
                }
            });
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    private function clockOut($schedule, $now, $endTime, $user, $isNightShift)
    {
        try {
            DB::transaction(function () use ($schedule, $now, $endTime, $user, $isNightShift) {

                $this->timesheets = $this->getTimesheet($user->employeeId, $now, $isNightShift);
                $minuteEarly = $endTime->gt($now) ? (int) $now->diffInMinutes($endTime) : 0;
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
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    private function clockOutStatus(Carbon $now, Carbon $endTime)
    {
        if ($endTime->gt($now)) {
            return TimesheetStatus::EARLY_CLOCK_OUT_ID;
        }
        if ($this->timesheets && $this->timesheets->status == TimesheetStatus::LATE_CLOCK_IN_ID) {
            return TimesheetStatus::LATE_CLOCK_IN_ID;
        }

        return TimesheetStatus::VALID_ID;
    }

    private function createTimesheets($schedule, $user, int $minuteValue, Carbon $now, int $status, bool $isClockOut = false)
    {
        if ($isClockOut && $this->timesheets) {
            // Avoid creating another clockOut entry if one already exists
            return;
        }
        $this->timesheets = Timesheets::create([
            'employeeId' => $user->employeeId,
            'shiftId' => $schedule->shiftId ?? $schedule->id,
            'minute' . ($isClockOut ? 'Early' : 'Late') => $minuteValue,
            'clock' . ($isClockOut ? 'Out' : 'In') => $now,
            'status' => $status,
            'createdBy' => $user->employeeId,
            'createdByName' => $user->employee->name
        ]);
    }

    private function getTimesheet($employeeId, Carbon $now, $isNightShift = false): Timesheets | null
    {
        $yesterday = $now->copy()->subDay();
        $query = Timesheets::where('employeeId', $employeeId)
            ->where(function ($query) use ($now, $yesterday, $isNightShift) {
                if ($isNightShift) {
                    $query->whereBetween('clockIn', [$yesterday->toDateString(), $now->toDateString()])
                        ->whereNull('clockOut')
                        ->where(function ($query) use ($now) {
                            $query->whereRaw('TIMESTAMPDIFF(HOUR, clockIn, ?) <= ?', [$now, 9])
                                ->orWhereDate('clockIn', $now->toDateString());
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
        // dd($query->first());
        return $query->exists() ? $query->latest()->first() : null;
    }



    private function getSchedule($user): Schedule | Shift
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
}
