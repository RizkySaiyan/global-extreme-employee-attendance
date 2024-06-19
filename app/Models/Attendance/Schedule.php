<?php

namespace App\Models\Attendance;

use App\Models\Attendance\Traits\HasActivityScheduleProperty;
use App\Models\BaseModel;
use App\Models\Employee\Employee;
use App\Parser\Attendance\ScheduleParser;
use App\Services\Constant\Attendance\AttendanceType;
use GlobalXtreme\Parser\Trait\HasParser;
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
    public static function saveSchedule($scheduleable, $attributes)
    {
        $user = Auth::user();

        $createdBy = [
            'createdBy' => $user->employeeId,
            'createdByName' => $user->employee->name
        ];

        if (isset($attributes['fromDate']) && isset($attributes['toDate'])) {
            $dates = generate_date_ranges($attributes['fromDate'], $attributes['toDate']);

            $attributes = [];
            foreach ($dates as $date) {
                $schedule = self::updateOrCreate([
                    'employeeId' => $scheduleable->employeeId,
                    'date' => $date
                ], [
                    'employeeId' => $scheduleable->employeeId,
                    'date' => $date,
                    'type' => AttendanceType::LEAVE_ID,
                    'referenceId' => $scheduleable->id,
                    'reference' => $scheduleable::class
                ] + $createdBy);
            }
            return $schedule;
        } else {
            return self::updateOrCreate([
                'employeeId' => $attributes['employeeId'],
                'date' => $attributes['date']
            ], [
                'employeeId' => $attributes['employeeId'],
                'date' => $attributes['date'],
                'type' => $attributes['type'],
                'referenceId' => $scheduleable->id ?? null,
                'reference' => $scheduleable ? get_class($scheduleable) : null
            ] + $createdBy);
        }
    }
}
