<?php

namespace App\Models\Attendance;

use App\Models\Attendance\Traits\HasActivityShiftProperty;
use App\Models\BaseModel;
use App\Parser\Attendance\ShiftParser;
use GlobalXtreme\Parser\Trait\HasParser;

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
}
