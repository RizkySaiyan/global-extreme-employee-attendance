<?php

namespace App\Models\Attendance;

use App\Models\BaseModel;
use App\Models\Employee\Employee;
use App\Parser\Attendance\TimesheetParser;

class Timesheets extends BaseModel
{
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
        });
    }
}
