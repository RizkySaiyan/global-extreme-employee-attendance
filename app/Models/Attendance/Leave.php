<?php

namespace App\Models\Attendance;

use App\Models\Attendance\Traits\HasActivityLeaveProperty;
use App\Models\Attendance\Traits\SaveSchedule;
use App\Models\BaseModel;
use App\Models\Employee\Employee;
use App\Services\Constant\Attendance\AttendanceType;
use App\Services\Constant\Attendance\LeaveBalance;
use App\Services\Constant\Attendance\StatusType;
use App\Services\Constant\Employee\EmployeeUserRole;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Leave extends BaseModel
{
    use HasActivityLeaveProperty;

    protected $table = 'attendance_leaves';
    protected $guarded = ['id'];

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];

    /** SCOPES */
    public function scopeFilter($query, $request)
    {
        return $query->where(function ($query) use ($request) {
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
        });
    }

    /** RELATIONSHIP */
    public function schedule()
    {
        return $this->morphMany(Schedule::class, 'schedule', 'reference', 'referenceId');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employeeId');
    }

    /** STATIC FUNCTION */
    public static function getEmployeeLeaves($employeeId)
    {
        $year = now()->year;

        $leave = self::where('employeeId', $employeeId)
            ->where('status', StatusType::APPROVED_ID)
            ->whereYear('fromDate', $year)
            ->whereYear('toDate', $year)
            ->pluck('toDate', 'fromDate');

        $totalLeaves = 0;
        $leave->each(function ($endDate, $startDate) use (&$totalLeaves) {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);

            $days = $start->diffInDays($end) + 1;
            $totalLeaves += $days;
        });
        return $totalLeaves;
    }
}
