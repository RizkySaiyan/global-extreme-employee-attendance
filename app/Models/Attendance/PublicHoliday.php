<?php

namespace App\Models\Attendance;

use App\Models\Attendance\Traits\HasActivityPublicHolidayProperty;
use App\Models\BaseModel;
use App\Parser\Attendance\PublicHolidayParser;
use GlobalXtreme\Parser\Trait\HasParser;
use Illuminate\Support\Facades\DB;

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
}
