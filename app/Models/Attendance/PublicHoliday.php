<?php

namespace App\Models\Attendance;

use App\Models\Attendance\Traits\HasActivityPublicHolidayProperty;
use App\Models\BaseModel;
use App\Models\Employee\Employee;
use App\Parser\Attendance\PublicHolidayParser;
use App\Services\Constant\Attendance\AttendanceType;

class PublicHoliday extends BaseModel
{
    use HasActivityPublicHolidayProperty;

    protected $table = 'attendance_public_holidays';
    protected $guarded = ['id'];

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];

    protected $parserClass = PublicHolidayParser::class;

    /** RELATIONSHIP */

    public function schedule()
    {
        return $this->morphMany(Schedule::class, 'schedule', 'reference', 'referenceId');
    }

    /** SCOPES */

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($query) use ($request) {

            if ($request->has('fromDate') && $request->has('toDate')) {
                $query->ofDate('date', $request->fromDate, $request->toDate);
            }

            if ($request->has('assigned') && $request->assigned == 1) {
                $query->where('isAssigned', true);
            }

            if ($request->has('assigned') && $request->assigned == 0) {
                $query->where('isAssigned', false);
            }
        });
    }

    /** FUNCTION */

    public function delete()
    {
        if ($this->schedule) {
            errPublicHolidayAssigned('Cannot delete');
        }

        return parent::delete();
    }

    /** STATIC FUNCTION */

    public function assignPublicHoliday($user)
    {
        $employees = Employee::whereDoesntHave('schedules', function ($query) {
            $query->whereDate('date', $this->date)
                ->whereNot('type', AttendanceType::PUBLIC_HOLIDAY_ID);
        })->where('isResign', false)->get();

        foreach ($employees as $employee) {
            Schedule::updateOrCreate([
                'employeeId' => $employee->employeeId,
                'date' => $this->date
            ], [
                'employeeId' => $employee->id,
                'date' => $this->date,
                'type' => AttendanceType::PUBLIC_HOLIDAY_ID,
                'referenceId' => $this->id,
                'reference' => PublicHoliday::class,
                'createdBy' => $user->employeeId,
                'createdByName' => $user->employee->name,
            ]);
        }
        $this->update([
            'isAssigned' => true,
        ]);
    }
}
