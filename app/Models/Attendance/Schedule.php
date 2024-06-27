<?php

namespace App\Models\Attendance;

use App\Models\Attendance\Traits\HasActivityScheduleProperty;
use App\Models\BaseModel;
use App\Models\Employee\Employee;
use App\Parser\Attendance\ScheduleParser;
use App\Services\Constant\Attendance\AttendanceType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Schedule extends BaseModel
{
    use HasActivityScheduleProperty;

    protected $table = 'attendance_schedules';
    protected $guarded = ['id'];

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];

    public $parserClass = ScheduleParser::class;

    /** RELATIONSHIPS */

    public function scheduleable()
    {
        return $this->morphTo('scheduleable', 'reference', 'referenceId');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employeeId');
    }

    /** SCOPES */
    public function scopeFilter($query, $request)
    {
        return $query->where(function ($query) use ($request) {

            if ($request->has('fromDate') && $request->has('toDate')) {
                $query->ofDate('date', $request->fromDate, $request->toDate);
            }
        });
    }

    /** STATIC FUNCTION */
    public static function setWeeklyDayOff($year, $employee, $user)
    {
        $sundays = [];
        $date = Carbon::create($year, 1, 1);
        if ($date->dayOfWeek != Carbon::SUNDAY) {
            $date->modify('next sunday');
        }

        while ($date->year == $year) {
            $sundays[] = $date->format('Y-m-d');
            $date->addWeek();
        }

        $query = Employee::query();
        if ($employee) {
            $query->where('id', $employee->id);
        }

        $employees = $query->whereDoesntHave('schedules', function ($query) {
            $query->where('type', AttendanceType::WEEKLY_DAY_OFF_ID);
        })->get();

        foreach ($employees as $employee) {
            foreach ($sundays as $sunday) {
                Schedule::create([
                    'employeeId' => $employee->id,
                    'date' => $sunday,
                    'type' => AttendanceType::WEEKLY_DAY_OFF_ID,
                    'referenceId' => null,
                    'reference' => null,
                    'createdBy' => $user ? $user->employeeId : 'System',
                    'createdByName' => $user ? $user->employeeId : 'System',
                ]);
            }
        }
    }
}
