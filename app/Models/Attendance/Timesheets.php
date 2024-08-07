<?php

namespace App\Models\Attendance;

use App\Models\Attendance\Traits\HasActivityCorrectionProperty;
use App\Models\Attendance\Traits\HasActivityTimesheetProperty;
use App\Models\BaseModel;
use App\Models\Employee\Employee;
use App\Parser\Attendance\TimesheetParser;

class Timesheets extends BaseModel
{
    use HasActivityTimesheetProperty;

    protected $table = 'attendance_timesheets';
    protected $guarded = ['id'];

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
        'clockIn' => 'datetime',
        'clockOut' => 'datetime',
    ];

    public $parserClass = TimesheetParser::class;

    /** RELATIONSHIPS */

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shiftId');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employeeId');
    }

    public function correction()
    {
        return $this->hasOne(Correction::class, 'timesheetId');
    }

    /** FUNCTION */

    public function delete()
    {
        if ($this->correction) {
            $this->correction()->delete();
        }

        return parent::delete();
    }

    /** SCOPES */

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($query) use ($request) {
            if ($request->has('fromDate') && $request->has('toDate')) {
                $query->ofDate('createdAt', $request->fromDate, $request->toDate);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('shiftId')) {
                $query->where('shiftId', $request->shiftId);
            }

            if ($request->has('employeeId')) {
                $query->where('employeeId', $request->employeeId);
            }
        });
    }
}
