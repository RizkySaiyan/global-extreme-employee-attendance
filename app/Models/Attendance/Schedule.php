<?php

namespace App\Models\Attendance;

use App\Models\BaseModel;

class Schedule extends BaseModel
{

    protected $table = 'attendance_schedules';
    protected $guarded = ['id'];

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];

    /** RELATIONSHIPS */

    public function scheduleable()
    {
        return $this->morphTo('scheduleable', 'reference', 'referenceId')->whereNotNull('referenceId');
    }
}
