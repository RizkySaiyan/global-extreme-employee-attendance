<?php

namespace App\Models\Attendance;

use App\Models\Attendance\Traits\HasActivityPublicHolidayProperty;
use App\Models\BaseModel;

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

    /** RELATIONSHIP */
    public function schedule()
    {
        return $this->morphMany(Schedule::class, 'schedule', 'reference', 'referenceId');
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
