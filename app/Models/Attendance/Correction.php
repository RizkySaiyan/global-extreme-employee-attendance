<?php

namespace App\Models\Attendance;

use App\Models\Attendance\Traits\HasActivityCorrectionProperty;
use App\Models\BaseModel;
use App\Models\Employee\Employee;
use App\Parser\Attendance\CorrectionParser;

class Correction extends BaseModel
{
    use HasActivityCorrectionProperty;

    protected $table = 'attendance_corrections';
    protected $guarded = ['id'];

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];

    public $parserClass = CorrectionParser::class;

    /** RELATIONSHIPS */

    public function timesheet()
    {
        return $this->belongsTo(Timesheets::class, 'timesheetId');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employeeId');
    }
}
