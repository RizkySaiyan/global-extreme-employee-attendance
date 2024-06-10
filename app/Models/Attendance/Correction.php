<?php

namespace App\Models\Attendance;

use App\Models\BaseModel;

class Correction extends BaseModel
{
    protected $table = 'attendance_corrections';
    protected $guarded = ['id'];

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];

}
