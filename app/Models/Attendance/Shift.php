<?php

namespace App\Models\Attendance;

use App\Models\Attendance\Traits\HasActivityShiftProperty;
use App\Models\BaseModel;
use App\Parser\Attendance\ShiftParser;
use GlobalXtreme\Parser\Trait\HasParser;
use Illuminate\Support\Facades\DB;

class Shift extends BaseModel
{
    use HasActivityShiftProperty;

    protected $table = 'attendance_shifts';
    protected $guarded = ['id'];

    protected $parserClass = ShiftParser::class;

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];

    /** RELATIONSHIP */

    public function schedule()
    {
        return $this->morphMany(Schedule::class, 'schedule', 'reference', 'referenceId');
    }

    public function timesheet()
    {
        return $this->hasMany(Timesheets::class, 'shiftId');
    }

    /** FUNCTION */

    public function delete()
    {
        $this->load(['timesheet', 'schedule']);

        if ($this->timesheet->isNotEmpty()) {
            errShiftDelete('Shift already used on other table');
        }

        if ($this->schedule->isNotEmpty()) {
            errShiftDelete('Shift already used on other table');
        }

        return parent::delete();
    }
}
